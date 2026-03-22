const messagesEl = document.getElementById("messages");

export function addMessage(role, text) {
    const wrap = document.createElement("div");
    wrap.className = "msg";

    const label = document.createElement("div");
    label.className = "msg-label";
    label.textContent = role;

    const bubble = document.createElement("div");

    if (role === "Bexi") {
        bubble.innerHTML = marked.parse(text);
    } else {
        bubble.textContent = text;
    }

    wrap.appendChild(label);
    wrap.appendChild(bubble);

    messagesEl.appendChild(wrap);
    messagesEl.scrollTop = messagesEl.scrollHeight;

    return wrap;
}

export function addToolStep(step) {
    const wrap = document.createElement("div");
    wrap.className = "tool-step";

    const header = document.createElement("div");
    header.className = "tool-step-header";
    header.textContent = step.tool;

    const body = document.createElement("div");
    body.className = "tool-step-body";
    body.textContent =
        `Args: ${JSON.stringify(step.args, null, 2)}\nResult: ${step.result}`;

    header.addEventListener("click", () => {
        body.classList.toggle("hidden");
    });

    wrap.appendChild(header);
    wrap.appendChild(body);
    messagesEl.appendChild(wrap);
}

export function setStatus(statusEl, msg, isError = false) {
    statusEl.textContent = msg;
    statusEl.style.color = isError ? "red" : "white";
}
