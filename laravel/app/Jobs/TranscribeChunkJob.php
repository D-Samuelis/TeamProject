<?php

namespace App\Jobs;

use App\Models\TranscriptionChunk;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class TranscribeChunkJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $chunkId;
    public $tries = 5;
    public $backoff = [30, 60, 120];

    public function __construct(int $chunkId)
    {
        $this->chunkId = $chunkId;
    }

    public function handle(): void
    {
        logger("Job started: {$this->chunkId}");

        $chunk = TranscriptionChunk::find($this->chunkId);

        if (!$chunk) {
            logger("Chunk not found: {$this->chunkId}");
            return;
        }

        $chunk->status = 'processing';
        $chunk->save();

        $audioPath = Storage::disk('chunks')->path($chunk->file_path);
        if (!file_exists($audioPath)) {
            logger("File missing: {$audioPath}");
            $chunk->status = 'failed';
            $chunk->save();
            return;
        }

        logger("Sending to Whisper...", ['path' => $audioPath]);

        try {
            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::timeout(120)
                ->withHeaders(['X-API-TOKEN' => env('WHISPER_API_TOKEN')])
                ->attach('file', fopen($audioPath, 'r'), basename($audioPath))
                ->post(env('WHISPER_URL') . '/transcribe');

        } catch (\Exception $e) {
            logger("Whisper request failed: " . $e->getMessage());
            // Let the job be retried by throwing an exception, or mark failed
            // throw $e;
            $chunk->status = 'failed';
            $chunk->save();
            return;
        }

        logger("Whisper responded", [
            'status' => $response->status(),
            'body' => substr($response->body(), 0, 2000),
        ]);

        if ($response->successful()) {
            $chunk->text = $response->json('text') ?? '';
            $chunk->status = 'done';
            $chunk->save();

            // optionally keep files for a short TTL in a separate archive; for now delete
            Storage::disk('chunks')->delete($chunk->file_path);
            logger("Chunk saved and deleted: {$this->chunkId}");
        } else {
            $chunk->status = 'failed';
            $chunk->save();
            logger("Whisper returned error for chunk {$this->chunkId}");
            // optionally throw to retry
            // throw new \Exception("Whisper response: " . $response->status());
        }
    }
}
