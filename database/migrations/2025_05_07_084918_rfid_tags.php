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
            $table->string('uid')->unique()->comment('UID dari RFID tag');
            $table->enum('status', ['Available', 'Used'])->default('Available');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Tambahkan kolom rfid_uid pada tabel user_details
        Schema::table('user_details', function (Blueprint $table) {
            $table->string('rfid_uid')->nullable()->after('pict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rfid_tags');

        Schema::table('user_details', function (Blueprint $table) {
            $table->dropColumn('rfid_uid');
        });
    }
};
