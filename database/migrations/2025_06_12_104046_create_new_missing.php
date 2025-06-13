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
        Schema::create('missing_tools', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->string('epc');
            $table->string('nama_barang');
            $table->unsignedBigInteger('user_id');
            $table->datetime('reported_at');
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->datetime('reclaimed_at')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Indexes
            // Indexes
            $table->index(['status', 'reported_at']);
            $table->index('user_id');
            $table->index('item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('missing_tools');
    }
};
