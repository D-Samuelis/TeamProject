import json
import uuid
import os
import re
from contextlib import asynccontextmanager
from pathlib import Path
from fastapi import FastAPI, HTTPException, Header
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from mcp import ClientSession
from mcp.client.streamable_http import streamablehttp_client
from openai import OpenAI
from dotenv import load_dotenv

load_dotenv()

MCP_URL   = os.getenv("MCP_SERVER")
MODEL     = os.getenv("MCP_OPENAI_MODEL", "gpt-4o")
API_KEY   = os.getenv("OPENAI_API_KEY")

PROMPTS_DIR  = Path("prompts")
PROMPT_FILES = ["identity.md", "capabilities.md", "tools.md", "rules.md"]

client = OpenAI(api_key=API_KEY)

sessions: dict[str, list] = {}


def load_system_prompt() -> str:
    parts = []
    for filename in PROMPT_FILES:
        path = PROMPTS_DIR / filename
        if path.exists():
            parts.append(path.read_text().strip())
        else:
            print(f"[warn] prompts/{filename} not found, skipping")
    return "\n\n---\n\n".join(parts)

SYSTEM_PROMPT = load_system_prompt()


def mcp_tool_to_openai(tool) -> dict:
    """Convert MCP tool to OpenAI's tool format."""
    return {
        "type": "function",
        "function": {
            "name": tool.name,
            "description": tool.description or "",
            "parameters": tool.inputSchema or {"type": "object", "properties": {}},
        },
    }


# ── Navigation helpers ────────────────────────────────────────────────────────

TOOL_ENTITY_MAP = {
    "list-services-tool":   "service",
    "list-businesses-tool": "business",
    "list-branches-tool":   "branch",
}


def _parse_entities(result_text: str, entity_type: str) -> list[dict]:
    stripped = result_text.strip()
    record_boundary = re.compile(rf'(?={re.escape(entity_type)} id\s*:)', re.IGNORECASE)
    raw_records = [r.strip() for r in record_boundary.split(stripped) if r.strip()]
    if not raw_records:
        raw_records = [stripped]

    entities = []
    kv_pattern = re.compile(r'[\w\s]+:\s*[^,\n]+')

    for record in raw_records:
        pairs: dict[str, str] = {}
        for match in kv_pattern.finditer(record):
            token = match.group(0).strip().rstrip(",")
            if ":" not in token:
                continue
            key, _, value = token.partition(":")
            key = re.sub(rf'^{re.escape(entity_type)} ', "", key.strip().lower())
            pairs[key] = value.strip()

        if eid := pairs.get("id"):
            entities.append({
                "id":          eid,
                "name":        pairs.get("name") or eid,
                "business_id": pairs.get("business_id"),
            })

    return entities



def build_navigations(steps: list[dict]) -> list[dict]:
    seen: dict[tuple, dict] = {}

    for step in steps:
        entity_type = TOOL_ENTITY_MAP.get(step.get("tool", ""))
        if not entity_type:
            continue

        for entity in _parse_entities(step.get("result", ""), entity_type):
            eid = entity["id"]
            url = _build_url(entity_type, eid, entity.get("business_id"))
            if url is None:
                continue

            seen[(entity_type, eid)] = {
                "type":  entity_type,
                "id":    eid,
                "name":  entity["name"],
                "url":   url,
                "label": f"View {entity['name']}",
            }

    return list(seen.values())


def _build_url(entity_type: str, entity_id: str, business_id_ctx: str | None) -> str | None:
    """Build the frontend URL for an entity."""
    match entity_type:
        case "business":
            return f"/book/business/{entity_id}"
        case "service":
            if business_id_ctx is None:
                return None  # can't build without business scope
            return f"/book/business/{business_id_ctx}/service/{entity_id}"
        case "branch":
            if business_id_ctx is None:
                return None
            return f"/book/business/{business_id_ctx}?branch_id={entity_id}"
        case _:
            return None


# ── Agent ─────────────────────────────────────────────────────────────────────

async def agent_turn(messages: list, token: str) -> dict:
    steps = []

    full_messages = [{"role": "system", "content": SYSTEM_PROMPT}] + messages

    async with streamablehttp_client(
        MCP_URL,
        headers={"Authorization": f"Bearer {token}"}
    ) as (read, write, _):
        async with ClientSession(read, write) as mcp:
            await mcp.initialize()
            tools = [mcp_tool_to_openai(t) for t in (await mcp.list_tools()).tools]

            while True:
                response = client.chat.completions.create(
                    model=MODEL,
                    messages=full_messages,
                    tools=tools or None,
                    tool_choice="auto",
                )

                msg = response.choices[0].message
                full_messages.append(msg)
                messages.append(msg)

                if not msg.tool_calls:
                    navigations = build_navigations(steps)
                    return {
                        "reply":       msg.content or "",
                        "steps":       steps,
                        "navigations": navigations,
                    }

                for tc in msg.tool_calls:
                    tool_name = tc.function.name
                    tool_args = json.loads(tc.function.arguments)

                    result = await mcp.call_tool(tool_name, tool_args)
                    result_text = "\n".join(
                        b.text for b in result.content if hasattr(b, "text")
                    )

                    tool_msg = {
                        "role": "tool",
                        "tool_call_id": tc.id,
                        "content": result_text,
                    }
                    full_messages.append(tool_msg)
                    messages.append(tool_msg)
                    steps.append({
                        "tool":   tool_name,
                        "args":   tool_args,
                        "result": result_text,
                    })


# ── FastAPI app ───────────────────────────────────────────────────────────────

@asynccontextmanager
async def lifespan(app: FastAPI):
    yield

app = FastAPI(lifespan=lifespan)
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_methods=["*"],
    allow_headers=["*"],
)


class NewSessionResponse(BaseModel):
    session_id: str

class ChatRequest(BaseModel):
    session_id: str
    message: str
    history: list[dict] = []

class ToolStep(BaseModel):
    tool: str
    args: dict
    result: str

class NavigationSuggestion(BaseModel):
    type:  str          # "business" | "service" | "branch"
    id:    str
    name:  str
    url:   str          # e.g. "/book/business/7/service/41"
    label: str          # e.g. "View Private Sauna Session"

class ChatResponse(BaseModel):
    reply:       str
    steps:       list[ToolStep]             = []
    navigations: list[NavigationSuggestion] = []


@app.post("/session", response_model=NewSessionResponse)
def create_session():
    sid = str(uuid.uuid4())
    sessions[sid] = []
    return {"session_id": sid}

@app.post("/chat", response_model=ChatResponse)
async def chat(req: ChatRequest, authorization: str = Header(...)):
    token = authorization.removeprefix("Bearer ").strip()
    if req.session_id not in sessions:
        sessions[req.session_id] = req.history or []

    messages = sessions[req.session_id]
    messages.append({"role": "user", "content": req.message})

    turn = await agent_turn(messages, token)
    return {
        "reply":       turn["reply"],
        "steps":       turn["steps"],
        "navigations": turn["navigations"],
    }

@app.delete("/session/{session_id}")
def delete_session(session_id: str):
    sessions.pop(session_id, None)
    return {"ok": True}

@app.get("/tools")
async def list_tools(authorization: str = Header(...)):
    token = authorization.removeprefix("Bearer ").strip()
    async with streamablehttp_client(MCP_URL, headers={"Authorization": f"Bearer {token}"}) as (read, write, _):
        async with ClientSession(read, write) as session:
            await session.initialize()
            tools = await session.list_tools()
            return {"tools": [t.name for t in tools.tools]}