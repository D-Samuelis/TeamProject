<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TranscriptionSession extends Model
{
    protected $fillable = ['session_id', 'final_text'];

    public function chunks()
    {
        return $this->hasMany(TranscriptionChunk::class, 'session_id');
    }
}
