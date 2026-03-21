import { createSession, getTools } from "./api.js";
import { setStatus } from "./ui.js";
import { handleMessage } from "./chat.js";

let sessionId = null;

const inputEl = document.getElementById("msg-input");
const sendBtn = document.getElementById("send-btn");
const statusEl = document.getElementById("status");
const toolsBadge = document.getElementById("tools-badge");
const messagesEl = document.getElementById("messages");

async function init() {
    try {
        const sd = await createSession();
        sessionId = sd.session_id;

        const td = await getTools();
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

init();
