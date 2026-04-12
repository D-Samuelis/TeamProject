<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class ChatbotController extends Controller
{
    public function index()
    {
        return view('chatbot', [
            'userId' => auth()->id()
        ]);
    }

    public function issueToken()
    {
        $user = auth()->user();
        $user->tokens()->where('name', 'mcp-client')->delete();
        $token = $user->createToken('mcp-client')->plainTextToken;

        return response()->json(['token' => $token]);
    }
}






