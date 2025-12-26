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
        Schema::create('pengiriman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanan');
            $table->string('kurir', 50);
            $table->string('nomor_resi', 100)->nullable();
            $table->text('alamat_tujuan');
            $table->decimal('biaya_ongkir', 10, 2)->default(0);
            $table->enum('status_pengiriman', [
                'siap_kirim',
                'dalam_perjalanan',
                'terkirim',
                'retur'
            ])->default('siap_kirim');
            $table->timestamp('update_terakhir')->useCurrent()->useCurrentOnUpdate();
            
            // Index for tracking
            $table->index('nomor_resi', 'idx_resi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengiriman');
    }
};
