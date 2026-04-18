@auth
    <meta name="user-id" content="{{ auth()->id() }}">

    <div id="chat-widget">
        @include('components.ui.snackbars')


        <div id="chat-panel" class="chat-hidden">
            <div id="chat-header">
                <span>BEXI</span>
                <span id="status">Loading…</span>
                <button id="chat-close">✕</button>
            </div>
            <div id="messages"></div>
            <div id="input-bar">
                <textarea id="msg-input" placeholder="Message…"></textarea>
                <button id="send-btn">Send</button>
                <button id="clear-btn">Clear</button>
            </div>
        </div>

        <button id="chat-toggle">🤖</button>
    </div>

    @vite('resources/js/chatbot/main.js')
@endauth
