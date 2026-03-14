import { sendChat } from "./api.js";
import { addMessage, addToolStep, setStatus } from "./ui.js";

export async function handleMessage(sessionId, text, elements) {
    const { sendBtn, inputEl, statusEl, messagesEl } = elements;

    inputEl.value = "";
    sendBtn.disabled = true;

    addMessage("user", text);

    const thinkingMsg = addMessage("bot", "thinking…");

    try {
        const data = await sendChat(sessionId, text);

        messagesEl.removeChild(thinkingMsg);

        for (const step of data.steps || []) {
            addToolStep(step);
        }

        addMessage("bot", data.reply);

        setStatus(
            statusEl,
            data.steps?.length
                ? `${data.steps.length} tool call(s)`
                : "Ready"
        );
    } catch (e) {
        messagesEl.removeChild(thinkingMsg);
        addMessage("bot", "Error: " + e.message);
        setStatus(statusEl, e.message, true);
    }

    sendBtn.disabled = false;
    inputEl.focus();
}
