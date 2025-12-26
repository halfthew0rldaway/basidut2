<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('kategori')->insert([
            ['id' => 1, 'nama' => 'Elektronik'],
            ['id' => 2, 'nama' => 'Fashion'],
            ['id' => 3, 'nama' => 'Rumah Tangga'],
        ]);
    }
}
