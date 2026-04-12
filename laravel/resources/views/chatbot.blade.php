@extends('web.layouts.app')
@section('content')

<style>
    .container {
        width: 60%;
    }

    #messages {
        padding: 1rem;
        height: 50vh;
        overflow-y: auto;
        border: 1px solid var(--color-border);
        background-color: var(--color-bg-complement);
        border-radius: .25rem;
        box-shadow: inset 0 0 5px var(--color-box-shadow-input);
    }

    /* Header */

    header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    h1 {
        letter-spacing: .2rem;
        font-size: 24px;
        font-weight: 600;
        color: var(--color-text);
    }

    #status {
        font-size: .85rem;
        color: var(--color-text-unimportant);
    }

    #tools-badge {
        font-size: .8rem;
        padding: .25rem .75rem;
        border-radius: .25rem;
        background: var(--color-bg-complement);
        border: 1px solid var(--color-border);
        color: var(--color-text-unimportant);
        margin-top: 1rem;
    }

    /* Messages */

    .msg {
        margin-bottom: .75rem;
        display: flex;
        flex-direction: column;
        gap: .2rem;
    }

    .msg-label {
        font-size: .75rem;
        letter-spacing: .1rem;
        text-transform: uppercase;
        color: var(--color-text-unimportant);
    }

    .msg div:last-child {
        padding: .6rem .9rem;
        background: var(--color-bg-complement);
        border: 1px solid var(--color-border);
        border-radius: .25rem;
        color: var(--color-text);
        max-width: 70%;
    }

    /* Tool steps */

    .tool-step {
        border: 1px solid var(--color-border);
        background: var(--color-bg-complement);
        border-radius: .25rem;
        margin-bottom: .5rem;
        overflow: hidden;
        transition: all .2s ease;
    }

    .tool-step-header {
        padding: .4rem .75rem;
        font-weight: 600;
        font-size: .8rem;
        letter-spacing: .05rem;
        cursor: pointer;
        background: var(--color-bg);
        color: var(--color-text);
        border-bottom: 1px solid var(--color-border);
    }

    .tool-step-header:hover {
        background: var(--color-button-hover);
    }

    .tool-step-body {
        padding: .5rem .75rem;
        font-family: monospace;
        font-size: .75rem;
        white-space: pre-wrap;
        color: var(--color-text-unimportant);
    }

    .tool-step-body.hidden {
        display: none;
    }

    /* Input bar */

    #input-bar {
        display: flex;
        gap: .75rem;
        margin-top: 1rem;
    }

    #msg-input {
        flex: 1;
        padding: .6rem .9rem;
        border-radius: .25rem;
        border: none;
        background-color: var(--color-bg-complement);
        color: var(--color-text);
        box-shadow: inset 0 0 5px var(--color-box-shadow-input);
        transition: all .2s ease;
    }

    #msg-input:focus {
        outline: 1px solid var(--color-primary);
    }

    button {
        padding: .55rem 1.2rem;
        border-radius: .25rem;
        border: 1px solid var(--color-primary);
        background: var(--color-primary);
        color: var(--color-text-white);
        font-weight: 500;
        transition: all .2s ease;
    }

    button:hover:not(:disabled) {
        background: var(--color-primary-hover);
        border-color: var(--color-primary-hover);
    }

    button:disabled {
        opacity: .5;
        cursor: not-allowed;
    }
</style>

<meta name="user-id" content="{{ $userId }}">

<div class="container">

    <header>
        <h1>BEXI 🤖</h1>
        <div id="status">
            Give me a second, I'm loading...
        </div>
    </header>


    <div id="messages"></div>

    <div id="input-bar">
        <textarea id="msg-input" rows="2" placeholder="Type a message…"></textarea>
        <button id="send-btn" disabled>Send</button>
        <button id="clear-btn">Clear history</button>
    </div>

    <div id="tools-badge">Connecting…</div>


</div>


@vite('resources/js/chatbot/main.js')

@endsection
