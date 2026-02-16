<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TranscriptionSession;

class TranscriptionChunk extends Model
{
    protected $fillable = [
        'session_id',
        'chunk_index',
        'file_path',
        'text'
    ];

    public function session()
    {
        return $this->belongsTo(TranscriptionSession::class, 'session_id');
    }
}
