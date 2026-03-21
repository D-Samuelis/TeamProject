import { sendChat } from "./api.js";
import { addMessage, addToolStep, setStatus } from "./ui.js";

export async function handleMessage(sessionId, text, elements) {
    const { sendBtn, inputEl, statusEl, messagesEl } = elements;

    inputEl.value = "";
    sendBtn.disabled = true;

    addMessage("You", text);

    const thinkingMsg = addMessage("Bexi", "Let me think…");

    try {
        const data = await sendChat(sessionId, text);

        messagesEl.removeChild(thinkingMsg);

        for (const step of data.steps || []) {
            addToolStep(step);
        }

        addMessage("Bexi", data.reply);

    } catch (e) {
        messagesEl.removeChild(thinkingMsg);
        addMessage("Bexi", "Error: " + e.message);
        setStatus(statusEl, e.message, true);
    }

    sendBtn.disabled = false;
    inputEl.focus();
}
