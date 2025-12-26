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
        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 200);
            $table->decimal('harga', 10, 2);
            $table->string('sku', 50)->unique();
            $table->foreignId('kategori_id')->constrained('kategori');
            $table->integer('stok')->default(0);
            $table->boolean('aktif')->default(true);
            
            // Indexes for performance
            $table->index('kategori_id', 'idx_produk_kategori');
        });
        
        // Add CHECK constraints using raw SQL
        DB::statement('ALTER TABLE produk ADD CONSTRAINT chk_harga_positif CHECK (harga >= 0)');
        DB::statement('ALTER TABLE produk ADD CONSTRAINT chk_stok_positif CHECK (stok >= 0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
