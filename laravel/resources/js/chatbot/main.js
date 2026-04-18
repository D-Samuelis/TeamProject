import { createSession, getTools } from "./api.js";
import { setStatus, addMessage } from "./ui.js";
import { handleMessage, clearHistory } from "./chat.js";

let sessionId = null;

const inputEl    = document.getElementById("msg-input");
const sendBtn    = document.getElementById("send-btn");
const statusEl   = document.getElementById("status");
const messagesEl = document.getElementById("messages");
const clearBtn   = document.getElementById("clear-btn");
const panel      = document.getElementById("chat-panel");
const widget     = document.getElementById("chat-widget");

function openSidebar() {
    panel.classList.remove("chat-hidden");
    widget.classList.remove("sidebar-closed");
    document.body.classList.add("sidebar-open");
    sessionStorage.setItem("bexi-open", "true");
}

function closeSidebar() {
    panel.classList.add("chat-hidden");
    widget.classList.add("sidebar-closed");
    document.body.classList.remove("sidebar-open");
    sessionStorage.setItem("bexi-open", "false");
}

const wasOpen = sessionStorage.getItem("bexi-open") === "true";
if (wasOpen) {
    openSidebar();
} else {
    widget.classList.add("sidebar-closed");
}

document.getElementById("chat-toggle").addEventListener("click", openSidebar);
document.getElementById("chat-close").addEventListener("click", closeSidebar);

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
            const history = JSON.parse(localStorage.getItem(storageKey("chat_history")) || "[]");
            for (const msg of history) {
                if (msg.role === "user") {
                    addMessage("You", msg.content);
                } else if (msg.role === "assistant" && msg.content) {
                    addMessage("Bexi", msg.content);
                }
            }
        }

        await getTools();

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
    handleMessage(sessionId, text, { sendBtn, inputEl, statusEl, messagesEl });
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

window.bexiOpenAndSend = (text) => {
    openSidebar();

    if (!sessionId) {
        const poll = setInterval(() => {
            if (!sessionId) return;
            clearInterval(poll);
            handleMessage(sessionId, text, { sendBtn, inputEl, statusEl, messagesEl });
        }, 100);
        return;
    }

    handleMessage(sessionId, text, { sendBtn, inputEl, statusEl, messagesEl });
};

init();
