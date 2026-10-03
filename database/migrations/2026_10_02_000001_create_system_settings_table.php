<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // integer, string, boolean, json
            $table->string('group')->default('security');
            $table->string('label')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Seed initial default security settings
        DB::table('system_settings')->insert([
            [
                'key' => 'max_login_attempts',
                'value' => '3',
                'type' => 'integer',
                'group' => 'security',
                'label' => 'Maximum Failed Login Attempts',
                'description' => 'Number of consecutive incorrect password attempts before the account/IP is temporarily locked out.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'lockout_duration_seconds',
                'value' => '30',
                'type' => 'integer',
                'group' => 'security',
                'label' => 'Lockout Penalty Duration (Seconds)',
                'description' => 'Duration in seconds the user must wait before attempting to sign in again after reaching maximum failed attempts.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
