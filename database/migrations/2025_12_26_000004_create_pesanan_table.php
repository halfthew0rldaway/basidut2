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
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pesanan', 50)->unique();
            $table->foreignId('pelanggan_id')->constrained('pengguna');
            $table->timestamp('tanggal_pesanan')->useCurrent();
            $table->decimal('total', 10, 2)->default(0);
            $table->enum('status', [
                'menunggu',
                'dibayar',
                'dikemas',
                'dikirim',
                'selesai',
                'dibatalkan'
            ])->default('menunggu');
            
            // Index for performance
            $table->index('pelanggan_id', 'idx_pesanan_pelanggan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};
