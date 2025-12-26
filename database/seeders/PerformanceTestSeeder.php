<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PerformanceTestSeeder extends Seeder
{
    /**
     * Seed large dataset for performance testing.
     * 
     * Creates:
     * - 1000 additional products
     * - 500 orders
     * - 1500 order items
     * - 500 shipping records
     */
    public function run(): void
    {
        echo "Seeding performance test data...\n";
        
        // 1. Create 1000 additional products
        echo "Creating 1000 products...\n";
        $products = [];
        for ($i = 4; $i <= 1003; $i++) {
            $products[] = [
                'nama' => "Produk Test {$i}",
                'harga' => rand(10000, 10000000),
                'sku' => "TEST-" . str_pad($i, 6, '0', STR_PAD_LEFT),
                'kategori_id' => rand(1, 3),
                'stok' => rand(10, 500),
                'aktif' => true,
            ];
        }
        
        // Insert in chunks for better performance
        foreach (array_chunk($products, 100) as $chunk) {
            DB::table('produk')->insert($chunk);
        }
        
        // 2. Create 500 orders with items and shipping
        echo "Creating 500 orders with items...\n";
        for ($i = 1; $i <= 500; $i++) {
            $pelangganId = rand(1, 101); // Random user from seeded users
            $produkId = rand(1, 1003); // Random product
            $qty = rand(1, 5);
            
            // Get product price
            $produk = DB::table('produk')->where('id', $produkId)->first();
            $total = $produk->harga * $qty;
            
            // Create order
            $pesananId = DB::table('pesanan')->insertGetId([
                'nomor_pesanan' => 'ORD-PERF-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'pelanggan_id' => $pelangganId,
                'tanggal_pesanan' => now()->subDays(rand(0, 365)),
                'total' => $total,
                'status' => ['menunggu', 'dibayar', 'dikemas', 'dikirim', 'selesai'][rand(0, 4)],
            ]);
            
            // Create order items (1-3 items per order)
            $itemCount = rand(1, 3);
            for ($j = 0; $j < $itemCount; $j++) {
                $itemProdukId = rand(1, 1003);
                $itemProduk = DB::table('produk')->where('id', $itemProdukId)->first();
                $itemQty = rand(1, 3);
                
                DB::table('item_pesanan')->insert([
                    'pesanan_id' => $pesananId,
                    'produk_id' => $itemProdukId,
                    'jumlah' => $itemQty,
                    'harga_satuan' => $itemProduk->harga,
                ]);
            }
            
            // Create shipping record
            DB::table('pengiriman')->insert([
                'pesanan_id' => $pesananId,
                'kurir' => ['JNE', 'JNT', 'SiCepat', 'Pos Indonesia'][rand(0, 3)],
                'nomor_resi' => 'RESI' . str_pad($i, 10, '0', STR_PAD_LEFT),
                'alamat_tujuan' => "Jl. Test No. {$i}, Jakarta",
                'biaya_ongkir' => rand(10000, 50000),
                'status_pengiriman' => ['siap_kirim', 'dalam_perjalanan', 'terkirim'][rand(0, 2)],
                'update_terakhir' => now()->subDays(rand(0, 30)),
            ]);
            
            if ($i % 100 == 0) {
                echo "Created {$i} orders...\n";
            }
        }
        
        echo "Performance test data seeding completed!\n";
        echo "Total data:\n";
        echo "- Products: " . DB::table('produk')->count() . "\n";
        echo "- Orders: " . DB::table('pesanan')->count() . "\n";
        echo "- Order Items: " . DB::table('item_pesanan')->count() . "\n";
        echo "- Shipping: " . DB::table('pengiriman')->count() . "\n";
    }
}
