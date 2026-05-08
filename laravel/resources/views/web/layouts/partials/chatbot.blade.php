@auth
    <meta name="user-id" content="{{ auth()->id() }}">

    <div id="chat-widget">
        <div id="chat-panel" class="chat-hidden">
            <div id="chat-header">
                <div class="display-row">
                    <div class="display-column">
                        <span class="chat__title">BEXI</span>
                        <span class="chat__tagline">Effortless booking AI</span>
                    </div>
                    <div class="chat__status-container">
                        <span id="status">Loading…</span>
                    </div>
                </div>
            </div>
            <div id="messages"></div>
            @include('components.ui.snackbars')
            <div id="input-bar">
                <textarea id="msg-input" placeholder="Message…"></textarea>
                <div class="display-column">
                    <button id="mic-btn" title="Voice input">
                        <span class="material-icons">mic</span>
                    </button>
                    <button id="send-btn">Send</button>
                    <button id="clear-btn">Clear</button>
                </div>

            </div>
        </div>
    </div>
    @vite('resources/js/chatbot/main.js')
@endauth
