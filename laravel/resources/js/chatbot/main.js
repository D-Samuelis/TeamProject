import { createSession, getTools } from "./api.js";
import { setStatus, addMessage } from "./ui.js";
import { handleMessage, clearHistory } from "./chat.js";

let sessionId = null;

const inputEl    = document.getElementById("msg-input");
const sendBtn    = document.getElementById("send-btn");
const statusEl   = document.getElementById("status");
const messagesEl = document.getElementById("messages");
const clearBtn   = document.getElementById("clear-btn");
const panel      = document.getElementById('chat-panel');

const wasOpen = sessionStorage.getItem('bexi-open') === 'true';

if (wasOpen) panel.classList.remove('chat-hidden');
document.getElementById('chat-toggle').addEventListener('click', () => {
    const isHidden = panel.classList.toggle('chat-hidden');
    sessionStorage.setItem('bexi-open', !isHidden);
});
document.getElementById('chat-close').addEventListener('click', () => {
    panel.classList.add('chat-hidden');
    sessionStorage.setItem('bexi-open', 'false');
});

function storageKey(key) {
    return `${key}_${document.querySelector('meta[name="user-id"]').content}`;
}
async function init() {
    try {
        sessionId = localStorage.getItem(storageKey("chat_session_id"));

        if (!sessionId) {
            const sd  = await createSession();
            sessionId = sd.session_id;
            localStorage.setItem(storageKey("chat_session_id"), sessionId);
        } else {
            const history = JSON.parse(localStorage.getItem(storageKey("chat_history")) || "[]")
            for (const msg of history) {
                if (msg.role === "user") {
                    addMessage("You", msg.content);
                } else if (msg.role === "assistant" && msg.content) {
                    addMessage("Bexi", msg.content);
                }
            }
        }

        const td    = await getTools();
        const names = td.tools;

        setStatus(statusEl, "I'm ready!");
        sendBtn.disabled = false;
        inputEl.focus();

    } catch {
        setStatus(statusEl, "Cannot reach server", true);
    }
}

sendBtn.addEventListener("click", () => {
    const text = inputEl.value.trim();
    if (!text || !sessionId) return;

    handleMessage(sessionId, text, {
        sendBtn,
        inputEl,
        statusEl,
        messagesEl
    });
});

inputEl.addEventListener("keydown", e => {
    if (e.key === "Enter" && !e.shiftKey) {
        e.preventDefault();
        sendBtn.click();
    }
});

clearBtn?.addEventListener("click", () => {
    clearHistory(sessionId);
});

init();
