import os
from pathlib import Path
from dotenv import load_dotenv

load_dotenv()

MCP_URL = os.getenv("MCP_SERVER")
MODEL   = os.getenv("MCP_OPENAI_MODEL")
API_KEY = os.getenv("OPENAI_API_KEY")

PROMPTS_DIR  = Path("prompts")
PROMPT_FILES = ["identity.md", "tools.md", "rules.md", "suggestions.md"]


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