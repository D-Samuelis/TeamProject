<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class ChatbotController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $user->tokens()->where('name', 'mcp-client')->delete();

        $token = $user->createToken('mcp-client')->plainTextToken;

        return view('chatbot', compact('token'));
    }
}






