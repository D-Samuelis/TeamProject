<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatbotController extends Controller
{
    public function issueToken()
    {
        $user = auth()->user();
        $user->tokens()->where('name', 'mcp-client')->delete();
        $token = $user->createToken('mcp-client')->plainTextToken;

        return response()->json(['token' => $token]);
    }

    public function transcribe(Request $request)
    {
        $request->validate(['audio' => 'required|file|mimes:webm,wav,mp3,ogg|max:20480']);

        $response = Http::attach(
            'file',
            $request->file('audio')->get(),
            $request->file('audio')->getClientOriginalName()
        )->post(config('services.whisper.url') . '/transcribe', [
            'language' => 'sk',
        ]);

        if (!$response->ok()) {
            return response()->json(['error' => 'Transcription service unavailable'], 502);
        }

        return response()->json($response->json()); // { job_id: "..." }
    }

    public function transcribeResult(string $jobId)
    {
        $response = Http::get(config('services.whisper.url') . '/result/' . $jobId);

        if (!$response->ok()) {
            return response()->json(['error' => 'Result fetch failed'], 502);
        }

        return response()->json($response->json()); // { status, text }
    }
}






