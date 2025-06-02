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
        Schema::create('rfid_tags', function (Blueprint $table) {
            $table->id();
            $table->string('tag_id')->unique(); // ID tag RFID fisik (harus unik)
            $table->string('description')->nullable(); // Deskripsi opsional untuk tag
            $table->unsignedBigInteger('user_detail_id')->nullable(); // Null jika belum diassign ke user
            $table->boolean('is_active')->default(true); // Status aktif/nonaktif
            $table->timestamp('registered_at')->nullable(); // Kapan tag diregistrasi ke user
            $table->timestamp('last_used_at')->nullable(); // Terakhir digunakan
            $table->timestamps();

            // Foreign key ke tabel user_details
            $table->foreign('user_detail_id')->references('id')->on('user_details')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rfid_tags');
    }
};
