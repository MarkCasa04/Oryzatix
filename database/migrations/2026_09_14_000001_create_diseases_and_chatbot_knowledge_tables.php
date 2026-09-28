<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('diseases')) {
            Schema::create('diseases', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->unique();
                $table->string('scientific_name')->nullable();
                $table->string('image_path')->nullable();
                $table->text('description')->nullable();
                $table->text('symptoms')->nullable();
                $table->text('causes')->nullable();
                $table->text('prevention')->nullable();
                $table->text('recommended_treatment')->nullable();
                $table->json('chemical_treatments')->nullable();
                $table->json('organic_treatments')->nullable();
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('chatbot_knowledge')) {
            Schema::create('chatbot_knowledge', function (Blueprint $table) {
                $table->id();
                $table->string('question');
                $table->text('answer');
                $table->string('category')->default('General');
                $table->string('language')->default('all');
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_knowledge');
        Schema::dropIfExists('diseases');
    }
};
