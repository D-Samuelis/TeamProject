const API = import.meta.env.VITE_MCP_CLIENT_URL;

const authHeaders = () => ({
    "Content-Type": "application/json",
    "Authorization": `Bearer ${window.API_TOKEN}`
});

export async function createSession() {
    const res = await fetch(`${API}/session`, {
        method: "POST",
        headers: authHeaders()
    });
    return res.json();
}

export async function getTools() {
    const res = await fetch(`${API}/tools`, {
        headers: authHeaders()
    });
    return res.json();
}

export async function sendChat(sessionId, message) {
    const res = await fetch(`${API}/chat`, {
        method: "POST",
        headers: authHeaders(),
        body: JSON.stringify({ session_id: sessionId, message })
    });
    return res.json();
}
