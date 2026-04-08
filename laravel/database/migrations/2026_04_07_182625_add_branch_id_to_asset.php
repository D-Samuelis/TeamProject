<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('asset_branch');

        Schema::table('assets', function (Blueprint $table) {
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Business\Branch::class);
            $table->dropColumn('branch_id');
        });

        Schema::create('asset_branch', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['branch_id', 'asset_id']);
        });
    }
};
