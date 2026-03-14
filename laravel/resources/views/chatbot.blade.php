@extends('layouts.app')
@section('content')

<style>
    #messages { padding: 8px; height: 60vh; overflow-y: auto; border: 1px solid #ccc; }
    textarea { width: 100%; }
    button { cursor: pointer; }
    .msg { margin-bottom: 8px; }
    .msg-label { font-weight: bold; }
    #tools-badge { font-size: 0.9em; font-weight: bold; }
    .tool-step { border: 1px solid #ccc; padding: 4px; margin-bottom: 4px; }
    .tool-step-header { font-weight: bold; cursor: pointer; }
    .tool-step-body { display: block; padding-left: 12px; white-space: pre-wrap; }
    .tool-step-body.hidden { display: none; }
</style>

<meta name="csrf-token" content="{{ csrf_token() }}">

<div>

    <header>
        <div>
            <h1>MCP Chat</h1>
            <div id="status"></div>
        </div>
        <div id="tools-badge">connecting…</div>
    </header>

    <div id="messages"></div>

    <div id="input-bar">
        <textarea id="msg-input" rows="2" placeholder="Type a message…"></textarea>
        <button id="send-btn" disabled>Send</button>
    </div>

</div>


@vite('resources/js/chatbot/main.js')

@endsection
