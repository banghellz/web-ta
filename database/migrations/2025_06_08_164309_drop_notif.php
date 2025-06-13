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
        Schema::dropIfExists('notifications');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the old table if needed
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('notifiable_id')->nullable();
            $table->string('notifiable_type')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->string('priority')->default('normal');
            $table->string('icon')->nullable();
            $table->string('color')->default('blue');
            $table->timestamps();
        });
    }
};
