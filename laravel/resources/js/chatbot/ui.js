const messagesEl = document.getElementById("messages");

export function addMessage(role, text) {
    const wrap = document.createElement("div");
    wrap.className = "msg";

    const label = document.createElement("div");
    label.className = "msg-label";
    label.textContent = role;

    const bubble = document.createElement("div");
    bubble.className = "msg-bubble";

    if (role === "Bexi") {
        bubble.innerHTML = marked.parse(text);
        wrap.classList.add("msg-bot");
    } else {
        bubble.textContent = text;
        wrap.classList.add("msg-user");
    }

    wrap.appendChild(label);
    wrap.appendChild(bubble);

    messagesEl.appendChild(wrap);
    messagesEl.scrollTop = messagesEl.scrollHeight;

    return wrap;
}

export function setStatus(statusEl, msg, isError = false) {
    statusEl.textContent = msg;
    statusEl.style.color = isError ? "red" : "white";
}
