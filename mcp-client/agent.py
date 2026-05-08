import json
import re

from mcp import ClientSession
from mcp.client.streamable_http import streamablehttp_client
from openai import OpenAI

from config import API_KEY, MODEL, MCP_URL, SYSTEM_PROMPT
from mcp_utils import mcp_tool_to_openai
from navigation import build_navigations

client = OpenAI(api_key=API_KEY)


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
                    raw_reply = msg.content or ""
                    suggestions = []

                    matches = list(re.finditer(r'\[Suggested Response\]\s*(.+)', raw_reply, re.IGNORECASE))
                    if matches:
                        suggestions = [m.group(1).strip() for m in matches]
                        reply = raw_reply[:matches[0].start()].strip()
                    else:
                        reply = raw_reply

                    return {
                        "reply":       reply,
                        "steps":       steps,
                        "navigations": build_navigations(steps),
                        "suggestions": suggestions,
                    }

                for tc in msg.tool_calls:
                    tool_name = tc.function.name
                    tool_args = json.loads(tc.function.arguments)

                    result = await mcp.call_tool(tool_name, tool_args)
                    result_text = "\n".join(
                        b.text for b in result.content if hasattr(b, "text")
                    )

                    tool_msg = {
                        "role":         "tool",
                        "tool_call_id": tc.id,
                        "content":      result_text,
                    }
                    full_messages.append(tool_msg)
                    messages.append(tool_msg)
                    steps.append({"tool": tool_name, "args": tool_args, "result": result_text})