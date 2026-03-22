const API = import.meta.env.VITE_MCP_CLIENT_URL;

let _token = null;

async function getToken() {
    if (_token) return _token;

    const res = await fetch('/chatbot/token', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        credentials: 'include'
    });

    if (!res.ok) throw new Error('Failed to fetch token');

    const { token } = await res.json();
    _token = token;
    return _token;
}

const authHeaders = async () => ({
    "Content-Type": "application/json",
    "Authorization": `Bearer ${await getToken()}`
});

export async function createSession() {
    const res = await fetch(`${API}/session`, {
        method: "POST",
        headers: await authHeaders()
    });
    return res.json();
}

export async function getTools() {
    const res = await fetch(`${API}/tools`, {
        headers: await authHeaders()
    });
    return res.json();
}

export async function sendChat(sessionId, message) {
    const res = await fetch(`${API}/chat`, {
        method: "POST",
        headers: await authHeaders(),
        body: JSON.stringify({ session_id: sessionId, message })
    });
    return res.json();
}
