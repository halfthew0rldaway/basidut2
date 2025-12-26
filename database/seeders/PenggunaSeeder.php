<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PenggunaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seeds 100 test users + 1 admin user
     * All passwords are bcrypt hashed 'password123'
     */
    public function run(): void
    {
        $users = [];
        
        // Create 100 test users
        for ($i = 1; $i <= 100; $i++) {
            $users[] = [
                'username' => "user{$i}",
                'email' => "user{$i}@mail.com",
                'kata_sandi' => Hash::make('password123'), // bcrypt hash
                'nama_lengkap' => "Pengguna {$i}",
                'aktif' => true,
                'dibuat_pada' => now(),
            ];
        }
        
        // Add admin user
        $users[] = [
            'username' => 'basidut',
            'email' => 'basidut@jokowi.com',
            'kata_sandi' => Hash::make('password123'), // bcrypt hash
            'nama_lengkap' => 'Admin Basidut',
            'aktif' => true,
            'dibuat_pada' => now(),
        ];
        
        // Insert in chunks for better performance
        foreach (array_chunk($users, 50) as $chunk) {
            DB::table('pengguna')->insert($chunk);
        }
    }
}
