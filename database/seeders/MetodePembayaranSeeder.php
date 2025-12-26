<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MetodePembayaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('metode_pembayaran')->insert([
            ['id' => 1, 'nama' => 'Transfer Bank'],
            ['id' => 2, 'nama' => 'Kartu Kredit'],
        ]);
    }
}
