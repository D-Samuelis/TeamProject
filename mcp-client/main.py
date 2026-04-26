import uuid
from contextlib import asynccontextmanager

from fastapi import FastAPI, Header
from fastapi.middleware.cors import CORSMiddleware
from mcp import ClientSession
from mcp.client.streamable_http import streamablehttp_client

from agent import agent_turn
from config import MCP_URL
from models import ChatRequest, ChatResponse, NewSessionResponse

sessions: dict[str, list] = {}

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
    return turn

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