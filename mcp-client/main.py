import json
import uuid
import os
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
                    tools=tools or None,  # OpenAI errors if tools=[]
                    tool_choice="auto",
                )

                msg = response.choices[0].message
                full_messages.append(msg)
                messages.append(msg)

                if not msg.tool_calls:
                    return {"reply": msg.content or "", "steps": steps}

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
                    steps.append({"tool": tool_name, "args": tool_args, "result": result_text})


# ── FastAPI app ───────────────────────────────────────────────────────────────

@asynccontextmanager
async def lifespan(app: FastAPI):
    yield

app = FastAPI(lifespan=lifespan)
app.add_middleware(CORSMiddleware, allow_origins=["*"], allow_methods=["*"], allow_headers=["*"])


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

class ChatResponse(BaseModel):
    reply: str
    steps: list[ToolStep] = []


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
    return {"reply": turn["reply"], "steps": turn["steps"]}

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