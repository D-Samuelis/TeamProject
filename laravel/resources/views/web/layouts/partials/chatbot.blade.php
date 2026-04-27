@auth
    <meta name="user-id" content="{{ auth()->id() }}">

    <div id="chat-widget">
        @include('components.ui.snackbars')

        {{-- Sidebar panel --}}
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
            <div id="input-bar">
                <textarea id="msg-input" placeholder="Message…"></textarea>
                <div class="display-column">
                    <button id="send-btn">Send</button>
                    <button id="clear-btn">Clear</button>
                </div>
            </div>
        </div>
    </div>

    @vite('resources/js/chatbot/main.js')
@endauth
