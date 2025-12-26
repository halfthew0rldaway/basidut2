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
        Schema::create('log_audit', function (Blueprint $table) {
            $table->id();
            $table->string('nama_tabel', 100);
            $table->integer('id_record')->nullable();
            $table->enum('aksi', ['INSERT', 'UPDATE', 'DELETE']);
            $table->text('keterangan')->nullable();
            $table->string('user_pelaku', 50)->default('SYSTEM');
            $table->timestamp('waktu')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_audit');
    }
};
