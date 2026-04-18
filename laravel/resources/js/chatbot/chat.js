import { sendChat } from "./api.js";
import { addMessage, setStatus } from "./ui.js";
import { showNavigations } from "./snackbar.js";

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

export async function handleMessage(sessionId, text, elements) {
    const { sendBtn, inputEl, statusEl, messagesEl } = elements;

    inputEl.value    = "";
    sendBtn.disabled = true;

    addMessage("You", text);
    appendToHistory("user", text);

    const thinkingMsg = addMessage("Bexi", "Thinking...");

    try {
        const history = getStoredHistory();

        const data = await sendChat(sessionId, text, history);

        messagesEl.removeChild(thinkingMsg);

        addMessage("Bexi", data.reply);
        appendToHistory("assistant", data.reply);

        if (data.navigations?.length) {
            showNavigations(data.navigations);
        }

    } catch (e) {
        messagesEl.removeChild(thinkingMsg);
        addMessage("Bexi", "Error: " + e.message);
        setStatus(statusEl, e.message, true);
    }

    sendBtn.disabled = false;
    inputEl.focus();
}
