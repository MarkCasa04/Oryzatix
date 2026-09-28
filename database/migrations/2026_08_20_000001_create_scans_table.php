<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('image_path');
            $table->string('disease_name');
            $table->string('scientific_name')->nullable();
            $table->decimal('confidence', 5, 2);
            $table->enum('severity', ['mild', 'moderate', 'severe', 'healthy']);
            $table->text('notes')->nullable();
            $table->json('treatment_recommendation')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scans');
    }
};
