<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed in correct order due to foreign key dependencies
        $this->call([
            KategoriSeeder::class,
            PenggunaSeeder::class,
            ProdukSeeder::class,
            MetodePembayaranSeeder::class,
            // PerformanceTestSeeder::class, // Uncomment to seed 1000+ rows for performance testing
        ]);
    }
}
