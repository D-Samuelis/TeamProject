<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transcription_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->unique();
            $table->text('final_text')->nullable();
            $table->timestamps();
        });

        Schema::create('transcription_chunks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')
                  ->constrained('transcription_sessions')
                  ->cascadeOnDelete();

            $table->integer('chunk_index');
            $table->string('file_path');
            $table->text('text')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transcription_chunks');
        Schema::dropIfExists('transcription_sessions');
    }
};
