<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_peminjaman', function (Blueprint $table) {
            $table->id(); // id (int(11), primary key)

            $table->unsignedBigInteger('item_id'); // item_id (int(11))
            $table->string('item_name', 255); // item_name (varchar(255))

            $table->enum('activity_type', ['pinjam', 'kembali']); // activity_type (enum)

            $table->dateTime('timestamp'); // timestamp (datetime)

            $table->unsignedBigInteger('user_id'); // user_id (int(11))
            $table->string('username', 255); // username (varchar(255))
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_peminjaman');
    }
};
