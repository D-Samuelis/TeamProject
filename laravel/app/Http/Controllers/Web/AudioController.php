<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class AudioController extends Controller
{
    public function index()
    {
        return view('audio');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'audio' => 'required|file',
            'session_id' => 'required|string',
        ]);

        // Store uploaded audio in private chunks disk
        $originalExtension = $request->file('audio')->getClientOriginalExtension();
        $filename = Str::random(40) . '.' . $originalExtension;

        $path = $request->file('audio')->storeAs('', $filename, 'chunks');

        if (!$path) {
            logger("Failed to store audio file!");
            return response()->json(['status' => 'error'], 500);
        }

        // Get full system path
        $fullPath = Storage::disk('chunks')->path($path);

        logger("Uploaded audio path: " . $fullPath);

        // --- Call FastAPI Whisper ---
        try {
            $response = Http::attach(
                'file',
                file_get_contents($fullPath),
                $filename
            )->post('http://whisper:8000/transcribe');

            /** @var \Illuminate\Http\Client\Response $response */
            if ($response->successful()) {

                return response()->json([
                    'status' => 'done',
                    'text' => $response->json('text'),
                    'size' => $response->json('size'),
                ]);
            } else {
                logger("FastAPI transcription failed: " . $response->body());
                return response()->json(['status' => 'error', 'detail' => 'Transcription failed'], 500);
            }
        } catch (\Exception $e) {
            logger("Transcription request error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'detail' => $e->getMessage()], 500);
        }
    }
}
