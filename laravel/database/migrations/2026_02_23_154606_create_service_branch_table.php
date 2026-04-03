<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('branch_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->decimal('custom_price', 10, 2)->nullable();     // null = use base_price
            $table->integer('custom_duration_minutes')->nullable();  // null = use base_duration
            $table->text('custom_description')->nullable();
            $table->string('location_type')->nullable();             // null = use service default
            $table->boolean('is_enabled')->default(true);
            $table->timestamps(); // already there
            $table->unique(['branch_id', 'service_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_branch');
    }
};
