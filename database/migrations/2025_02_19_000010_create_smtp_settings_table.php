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
        Schema::create('smtp_settings', function (Blueprint $table) {
            $table->id();
            $table->string('host');
            $table->integer('port');
            $table->string('username');
            $table->string('password'); // Encrypted in the model
            $table->enum('encryption', ['tls', 'ssl', 'none'])->default('tls');
            $table->boolean('is_active')->default(true);
            $table->integer('max_per_minute')->default(60);
            $table->timestamp('last_tested_at')->nullable();
            $table->boolean('test_result')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('smtp_settings');
    }
};
