import asyncio
import json
import uuid
import httpx
import os
from contextlib import asynccontextmanager
from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from mcp import ClientSession
from mcp.client.streamable_http import streamablehttp_client
from dotenv import load_dotenv

load_dotenv()

OLLAMA_URL = os.getenv("MCP_OLLAMA_URL")
MCP_URL    = os.getenv("MCP_SERVER")
MODEL      = os.getenv("MCP_OLLAMA_MODEL")

# ── In-memory session store ───────────────────────────────────────────────────
# Each session_id maps to a list of messages (the conversation history)
sessions: dict[str, list] = {}

# Shared MCP state (tools fetched once at startup)
ollama_tools: list = []
mcp_session: ClientSession | None = None
_mcp_context = None


# ── MCP lifecycle ─────────────────────────────────────────────────────────────

@asynccontextmanager
async def lifespan(app: FastAPI):
    global mcp_session, ollama_tools, _mcp_context

    # Open MCP connection once when the server starts
    cm = streamablehttp_client(MCP_URL)
    read, write, _ = await cm.__aenter__()
    session = ClientSession(read, write)
    await session.__aenter__()
    await session.initialize()

    tools_result = await session.list_tools()
    ollama_tools = [mcp_tool_to_ollama(t) for t in tools_result.tools]
    mcp_session = session
    _mcp_context = (cm, session)

    print(f"[MCP] {len(ollama_tools)} tool(s): {[t['function']['name'] for t in ollama_tools]}")
    yield

    # Cleanup on shutdown
    await session.__aexit__(None, None, None)
    await cm.__aexit__(None, None, None)


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


async def call_ollama(messages: list, tools: list) -> dict:
    payload = {"model": MODEL, "messages": messages, "tools": tools, "stream": False}
    async with httpx.AsyncClient(timeout=120) as client:
        resp = await client.post(OLLAMA_URL, json=payload)
        resp.raise_for_status()
        return resp.json()["message"]


async def agent_turn(messages: list) -> dict:
    """Run the agentic loop for one user turn.
    Returns { reply, steps: [{tool, args, result}, ...] }
    """
    steps = []

    while True:
        assistant_msg = await call_ollama(messages, ollama_tools)
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

            result = await mcp_session.call_tool(tool_name, tool_args)
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
    """Create a new conversation session."""
    sid = str(uuid.uuid4())
    sessions[sid] = []
    return {"session_id": sid}


@app.post("/chat", response_model=ChatResponse)
async def chat(req: ChatRequest):
    """Send a message and get a reply. Maintains history per session."""
    if req.session_id not in sessions:
        raise HTTPException(status_code=404, detail="Session not found. Call POST /session first.")

    messages = sessions[req.session_id]
    messages.append({"role": "user", "content": req.message})

    turn = await agent_turn(messages)
    return {"reply": turn["reply"], "steps": turn["steps"]}


@app.delete("/session/{session_id}")
def delete_session(session_id: str):
    """Clear a session's history."""
    sessions.pop(session_id, None)
    return {"ok": True}


@app.get("/tools")
def list_tools():
    """Return available MCP tools."""
    return {"tools": [t["function"]["name"] for t in ollama_tools]}