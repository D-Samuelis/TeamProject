const API = "http://127.0.0.1:8001"
let sessionId = null;

const inputEl    = document.getElementById("msg-input");
const sendBtn    = document.getElementById("send-btn");
const statusEl   = document.getElementById("status");
const toolsBadge = document.getElementById("tools-badge");
const messagesEl = document.getElementById("messages");

async function init() {
    try {
        const sr = await fetch(`${API}/session`, { method: "POST" });
        const sd = await sr.json();
        sessionId = sd.session_id;

        const tr = await fetch(`${API}/tools`);
        const td = await tr.json();
        const names = td.tools;
        toolsBadge.textContent = `${names.length} tool${names.length !== 1 ? "s" : ""}: ${names.join(", ")}`;
        setStatus("Chatbot is ready");
        sendBtn.disabled = false;
        inputEl.focus();
    } catch (e) {
        setStatus("Cannot reach server", true);
        toolsBadge.textContent = "offline";
    }
}

function addMessage(role, text) {
    const wrap = document.createElement("div");
    wrap.className = "msg";
    const label = document.createElement("div");
    label.className = "msg-label";
    label.textContent = role;
    const bubble = document.createElement("div");
    bubble.textContent = text;
    wrap.appendChild(label);
    wrap.appendChild(bubble);
    messagesEl.appendChild(wrap);
    messagesEl.scrollTop = messagesEl.scrollHeight;
    return wrap;
}


function addToolStep(step) {
    const wrap = document.createElement("div");
    wrap.className = "tool-step";

    const header = document.createElement("div");
    header.className = "tool-step-header";
    header.textContent = step.tool;

    const body = document.createElement("div");
    body.className = "tool-step-body";
    body.textContent = `Args: ${JSON.stringify(step.args, null, 2)}\nResult: ${step.result}`;

    header.addEventListener("click", () => {
        body.classList.toggle("hidden");
    });

    wrap.appendChild(header);
    wrap.appendChild(body);
    messagesEl.appendChild(wrap);
    messagesEl.scrollTop = messagesEl.scrollHeight;
}


async function sendMessage() {
    const text = inputEl.value.trim();
    if (!text || !sessionId) return;
    inputEl.value = "";
    sendBtn.disabled = true;

    addMessage("user", text);

    const thinkingMsg = addMessage("bot", "thinking…");

    try {
        const res = await fetch(`${API}/chat`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ session_id: sessionId, message: text }),
        });
        const data = await res.json();

        messagesEl.removeChild(thinkingMsg);

        for (const step of data.steps || []) {
            addToolStep(step);
        }

        addMessage("bot", data.reply);
        setStatus(data.steps && data.steps.length > 0 ? `${data.steps.length} tool call(s)` : "Ready");
    } catch (e) {
        messagesEl.removeChild(thinkingMsg);
        addMessage("bot", "Error: " + e.message);
        setStatus(e.message, true);
    }
    sendBtn.disabled = false;
    inputEl.focus();
}

function setStatus(msg, isError = false) {
    statusEl.textContent = msg;
    statusEl.style.color = isError ? "red" : "white";
}

inputEl.addEventListener("keydown", e => {
    if (e.key === "Enter" && !e.shiftKey) {
        e.preventDefault();
        if (!sendBtn.disabled) sendMessage();
    }
});

sendBtn.addEventListener("click", sendMessage);

init();

