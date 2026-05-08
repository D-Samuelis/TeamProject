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
                </div>
                <button id="chat-close" onclick="closeSidebar()">
                    <span class="material-icons">close</span>
                </button>
            </div>
            <div id="messages"></div>
            @include('components.ui.snackbars')
            <div id="input-bar">
                <div id="input-box">
                    <textarea id="msg-input" placeholder="Message…" rows="1"></textarea>
                    <div id="input-actions">
                        <button id="clear-btn" title="Clear chat">
                            <span class="material-icons">delete_outline</span>
                        </button>
                        <button id="mic-btn" title="Voice input">
                            <span class="material-icons">mic</span>
                        </button>
                        <button id="send-btn" title="Send">
                            <span class="material-icons">send</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @vite('resources/js/chatbot/main.js')
@endauth
