from pydantic import BaseModel

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
    type:  str
    id:    str
    name:  str
    url:   str
    label: str

class ChatResponse(BaseModel):
    reply:       str
    steps:       list[ToolStep]             = []
    navigations: list[NavigationSuggestion] = []