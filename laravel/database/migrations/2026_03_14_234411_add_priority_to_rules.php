<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rules', function (Blueprint $table) {
            $table->unsignedInteger('priority')->default(1)->after('asset_id');

            // One priority number per asset
            $table->unique(['asset_id', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::table('rules', function (Blueprint $table) {
            $table->dropUnique(['asset_id', 'priority']);
            $table->dropColumn('priority');
        });
    }
};
