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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // 'user_registered', 'user_login', 'item_added', etc
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable(); // Additional data as JSON
            $table->unsignedBigInteger('user_id')->nullable(); // User yang menyebabkan notifikasi
            $table->unsignedBigInteger('notifiable_id')->nullable(); // ID admin yang akan menerima notif
            $table->string('notifiable_type')->nullable(); // Model admin yang akan menerima notif
            $table->timestamp('read_at')->nullable(); // Kapan notifikasi dibaca
            $table->string('priority')->default('normal'); // low, normal, high, urgent
            $table->string('icon')->nullable(); // Icon untuk notifikasi
            $table->string('color')->default('blue'); // Warna notifikasi
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['notifiable_id', 'notifiable_type']);
            $table->index(['read_at', 'created_at']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
