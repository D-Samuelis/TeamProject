import { sendChat } from "./api.js";
import { addMessage, addToolStep, setStatus } from "./ui.js";

// ── localStorage helpers ──────────────────────────────────────────────────────

function storageKey(key) {
    return `${key}_${document.querySelector('meta[name="user-id"]').content}`;
}

function getStoredHistory() {
    return JSON.parse(localStorage.getItem(storageKey("chat_history")) || "[]");
}

function appendToHistory(role, content) {
    const history = getStoredHistory();
    history.push({ role, content });
    localStorage.setItem(storageKey("chat_history"), JSON.stringify(history));
}

export function clearHistory(sessionId) {
    localStorage.removeItem(storageKey("chat_session_id"));
    localStorage.removeItem(storageKey("chat_history"));
    if (sessionId) {
        const API = import.meta.env.VITE_MCP_CLIENT_URL;
        navigator.sendBeacon(`${API}/session/${sessionId}`);
    }
    location.reload();
}

// ── Chat handler ──────────────────────────────────────────────────────────────

export async function handleMessage(sessionId, text, elements) {
    const { sendBtn, inputEl, statusEl, messagesEl } = elements;

    inputEl.value    = "";
    sendBtn.disabled = true;

    addMessage("You", text);
    appendToHistory("user", text);

    const thinkingMsg = addMessage("Bexi", "Let me think…");

    try {
        const history = getStoredHistory();

        const data = await sendChat(sessionId, text, history);

        messagesEl.removeChild(thinkingMsg);

        for (const step of data.steps || []) {
            addToolStep(step);
        }

        addMessage("Bexi", data.reply);
        appendToHistory("assistant", data.reply);

    } catch (e) {
        messagesEl.removeChild(thinkingMsg);
        addMessage("Bexi", "Error: " + e.message);
        setStatus(statusEl, e.message, true);
    }

    sendBtn.disabled = false;
    inputEl.focus();
}
