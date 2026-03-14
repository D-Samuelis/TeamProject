<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Assistant</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500&family=IBM+Plex+Sans:wght@300;400;500&display=swap" rel="stylesheet">
</head>
<body>


<div id="messages">
    <div id="empty">
    </div>
</div>

<div id="input-area">
    <div class="input-row">
        <textarea
            id="user-input"
            rows="1"
            placeholder="Send a message…"
            autocomplete="off"
            spellcheck="true"
        ></textarea>
        <button id="send-btn" title="Send">Send</button>
    </div>
</div>

<script>
    const messagesEl  = document.getElementById('messages');
    const inputEl     = document.getElementById('user-input');
    const sendBtn     = document.getElementById('send-btn');
    const emptyEl     = document.getElementById('empty');

    // Full conversation history (sent to server each time)
    let history = [];

    // ── Auto-resize textarea ────────────────────────────────────────────────────
    inputEl.addEventListener('input', () => {
        inputEl.style.height = 'auto';
        inputEl.style.height = Math.min(inputEl.scrollHeight, 140) + 'px';
    });

    // ── Send on Enter (not Shift+Enter) ────────────────────────────────────────
    inputEl.addEventListener('keydown', e => {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
    });
    sendBtn.addEventListener('click', sendMessage);

    // ── Render helpers ──────────────────────────────────────────────────────────
    function appendMessage(role, content, extra = {}) {
        emptyEl.style.display = 'none';

        const wrap = document.createElement('div');
        wrap.className = `msg ${role}`;
        if (extra.id) wrap.id = extra.id;

        const label = document.createElement('div');
        label.className = 'label';
        label.textContent = role === 'user' ? 'you' : 'assistant';

        const bubble = document.createElement('div');
        bubble.className = 'bubble';
        bubble.textContent = content;

        wrap.appendChild(label);
        wrap.appendChild(bubble);
        messagesEl.appendChild(wrap);
        messagesEl.scrollTop = messagesEl.scrollHeight;
        return wrap;
    }

    function showThinking() {
        emptyEl.style.display = 'none';
        const wrap = document.createElement('div');
        wrap.className = 'msg assistant thinking';
        wrap.id = 'thinking';
        wrap.innerHTML = `
        <div class="label">assistant</div>
        <div class="bubble"><span></span><span></span><span></span></div>`;
        messagesEl.appendChild(wrap);
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    function removeThinking() {
        document.getElementById('thinking')?.remove();
    }

    // ── Main send ───────────────────────────────────────────────────────────────
    async function sendMessage() {
        const text = inputEl.value.trim();
        if (!text) return;

        // Append user message to UI + history
        appendMessage('user', text);
        history.push({ role: 'user', content: text });

        // Reset input
        inputEl.value = '';
        inputEl.style.height = 'auto';
        sendBtn.disabled = true;
        showThinking();

        try {
            const res = await fetch('/chatbot/message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ messages: history }),
            });

            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            const data = await res.json();
            const reply = data.reply ?? '(empty response)';

            removeThinking();
            appendMessage('assistant', reply);
            history.push({ role: 'assistant', content: reply });

        } catch (err) {
            removeThinking();
            appendMessage('assistant', '⚠ Error: ' + err.message);
        } finally {
            sendBtn.disabled = false;
            inputEl.focus();
        }
    }
</script>
</body>
</html>
