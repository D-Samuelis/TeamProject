import { createSession, getTools } from "./api.js";
import { setStatus } from "./ui.js";
import { handleMessage, clearHistory } from "./chat.js";
import { addMessage } from "./ui.js";

let sessionId = null;

const inputEl    = document.getElementById("msg-input");
const sendBtn    = document.getElementById("send-btn");
const statusEl   = document.getElementById("status");
const toolsBadge = document.getElementById("tools-badge");
const messagesEl = document.getElementById("messages");
const clearBtn   = document.getElementById("clear-btn");

function storageKey(key) {
    return `${key}_${document.querySelector('meta[name="user-id"]').content}`;
}
async function init() {
    try {
        sessionId = localStorage.getItem(storageKey("chat_session_id"));

        if (!sessionId) {
            // Fresh session
            const sd  = await createSession();
            sessionId = sd.session_id;
            localStorage.setItem(storageKey("chat_session_id"), sessionId);
        } else {
            // Restore visual history from localStorage
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

        toolsBadge.textContent =
            `${names.length} tool${names.length !== 1 ? "s" : ""}: ${names.join(", ")}`;

        setStatus(statusEl, "I'm ready!");
        sendBtn.disabled = false;
        inputEl.focus();

    } catch {
        setStatus(statusEl, "Cannot reach server", true);
        toolsBadge.textContent = "offline";
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
