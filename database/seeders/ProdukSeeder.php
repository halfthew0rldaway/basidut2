<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('produk')->insert([
            [
                'id' => 1,
                'nama' => 'Laptop Pro',
                'harga' => 15000000.00,
                'sku' => 'LPT-001',
                'kategori_id' => 1, // Elektronik
                'stok' => 50,
                'aktif' => true,
            ],
            [
                'id' => 2,
                'nama' => 'Smartphone X',
                'harga' => 8000000.00,
                'sku' => 'HP-001',
                'kategori_id' => 1, // Elektronik
                'stok' => 100,
                'aktif' => true,
            ],
            [
                'id' => 3,
                'nama' => 'Kemeja Kantor',
                'harga' => 150000.00,
                'sku' => 'BJU-001',
                'kategori_id' => 2, // Fashion
                'stok' => 200,
                'aktif' => true,
            ],
        ]);
    }
}
