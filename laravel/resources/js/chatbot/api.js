const API = import.meta.env.VITE_MCP_CLIENT_URL;

export async function createSession() {
    const res = await fetch(`${API}/session`, { method: "POST" });
    return res.json();
}

export async function getTools() {
    const res = await fetch(`${API}/tools`);
    return res.json();
}

export async function sendChat(sessionId, message) {
    const res = await fetch(`${API}/chat`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ session_id: sessionId, message })
    });

    return res.json();
}
