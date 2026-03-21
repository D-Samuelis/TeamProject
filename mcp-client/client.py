import asyncio
import json
import uuid
import httpx
import os
from contextlib import asynccontextmanager
from fastapi import FastAPI, HTTPException, Header
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from mcp import ClientSession
from mcp.client.streamable_http import streamablehttp_client
from dotenv import load_dotenv

load_dotenv()

OLLAMA_URL = os.getenv("MCP_OLLAMA_URL")
MCP_URL    = os.getenv("MCP_SERVER")
MODEL      = os.getenv("MCP_OLLAMA_MODEL")

sessions: dict[str, list] = {}

ollama_tools: list = []
mcp_session: ClientSession | None = None
_mcp_context = None


@asynccontextmanager
async def lifespan(app: FastAPI):
    yield  # MCP connection is now per-request (needs auth token)


app = FastAPI(lifespan=lifespan)

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_methods=["*"],
    allow_headers=["*"],
)


# ── Helpers ───────────────────────────────────────────────────────────────────

def mcp_tool_to_ollama(tool) -> dict:
    return {
        "type": "function",
        "function": {
            "name": tool.name,
            "description": tool.description or "",
            "parameters": tool.inputSchema or {"type": "object", "properties": {}},
        },
    }


async def get_mcp_tools(token: str) -> list:
    """Fetch tools from MCP server using the user's token."""
    async with streamablehttp_client(
        MCP_URL,
        headers={"Authorization": f"Bearer {token}"}
    ) as (read, write, _):
        async with ClientSession(read, write) as session:
            await session.initialize()
            tools_result = await session.list_tools()
            return [mcp_tool_to_ollama(t) for t in tools_result.tools]


async def call_ollama(messages: list, tools: list) -> dict:
    payload = {"model": MODEL, "messages": messages, "tools": tools, "stream": False}
    async with httpx.AsyncClient(timeout=120) as client:
        resp = await client.post(OLLAMA_URL, json=payload)
        resp.raise_for_status()
        return resp.json()["message"]


async def agent_turn(messages: list, token: str) -> dict:
    """Run the agentic loop for one user turn."""
    steps = []

    async with streamablehttp_client(
        MCP_URL,
        headers={"Authorization": f"Bearer {token}"}
    ) as (read, write, _):
        async with ClientSession(read, write) as mcp:
            await mcp.initialize()
            tools = [mcp_tool_to_ollama(t) for t in (await mcp.list_tools()).tools]

            while True:
                assistant_msg = await call_ollama(messages, tools)
                messages.append(assistant_msg)

                tool_calls = assistant_msg.get("tool_calls") or []
                if not tool_calls:
                    return {"reply": assistant_msg.get("content", ""), "steps": steps}

                for tc in tool_calls:
                    fn        = tc["function"]
                    tool_name = fn["name"]
                    tool_args = fn.get("arguments", {})
                    if isinstance(tool_args, str):
                        tool_args = json.loads(tool_args)

                    result = await mcp.call_tool(tool_name, tool_args)
                    result_text = "\n".join(
                        block.text for block in result.content if hasattr(block, "text")
                    )
                    messages.append({"role": "tool", "content": result_text})
                    steps.append({"tool": tool_name, "args": tool_args, "result": result_text})


# ── API models ────────────────────────────────────────────────────────────────

class NewSessionResponse(BaseModel):
    session_id: str

class ChatRequest(BaseModel):
    session_id: str
    message: str

class ToolStep(BaseModel):
    tool: str
    args: dict
    result: str

class ChatResponse(BaseModel):
    reply: str
    steps: list[ToolStep] = []


# ── Routes ────────────────────────────────────────────────────────────────────

@app.post("/session", response_model=NewSessionResponse)
def create_session():
    sid = str(uuid.uuid4())
    sessions[sid] = []
    return {"session_id": sid}


@app.post("/chat", response_model=ChatResponse)
async def chat(
    req: ChatRequest,
    authorization: str = Header(...)  # expects "Bearer <token>"
):
    if req.session_id not in sessions:
        raise HTTPException(status_code=404, detail="Session not found. Call POST /session first.")

    token = authorization.removeprefix("Bearer ").strip()

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
    tools = await get_mcp_tools(token)
    return {"tools": [t["function"]["name"] for t in tools]}