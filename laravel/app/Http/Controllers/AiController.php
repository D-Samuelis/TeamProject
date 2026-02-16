<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AiController extends Controller
{
    public function chat(Request $request)
    {
        $response = Http::post('http://localhost:8080/chat', [
            'message' => $request->message,
        ]);

        return response()->json(['message' => $response->json()['message']]);
    }
}
