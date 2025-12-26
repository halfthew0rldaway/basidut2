# Panduan Setup Database

## ⚠️ Masalah Penting yang Ditemukan

File `basidut.sql` memiliki **password teks biasa** (`password123`), tetapi Laravel memerlukan **password yang di-hash dengan bcrypt**. Inilah mengapa login gagal bahkan setelah mengimpor database.

## 🔧 Solusi: Dua Pilihan

### Pilihan 1: Impor Database + Perbarui Password (Disarankan)

#### Langkah 1: Impor File SQL
```bash
# Menggunakan command line MySQL
mysql -u root -p < basidut.sql

# Atau menggunakan HeidiSQL:
# 1. Buka HeidiSQL
# 2. Sambungkan ke server MySQL Anda
# 3. File > Run SQL file > Pilih basidut.sql
# 4. Klik Execute
```

#### Langkah 2: Perbarui Password ke Hash Bcrypt
Setelah mengimpor, jalankan SQL ini untuk memperbarui semua password ke versi hash bcrypt:

```sql
USE basidut;

-- Perbarui semua password pengguna ke hash bcrypt dari 'password123'
-- Hash: $2y$12$bz3jN1tD/7AAwNFO6k2ln.jxnxuAC9A3qGkAiirQ4/fs2z9wE9/XK
UPDATE pengguna 
SET kata_sandi = '$2y$12$bz3jN1tD/7AAwNFO6k2ln.jxnxuAC9A3qGkAiirQ4/fs2z9wE9/XK';
```

#### Langkah 3: Verifikasi
```sql
-- Periksa apakah password sudah diperbarui
SELECT id, username, email, LEFT(kata_sandi, 20) as password_hash 
FROM pengguna 
LIMIT 5;
```

### Pilihan 2: Gunakan Laravel Seeder (Alternatif)

Buat seeder untuk mengisi database dengan password bcrypt yang benar:

```bash
php artisan make:seeder PenggunaSeeder
```

Kemudian jalankan:
```bash
php artisan db:seed --class=PenggunaSeeder
```

## ✅ Setelah Setup

1. **Verifikasi koneksi database di .env:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=basidut
DB_USERNAME=root
DB_PASSWORD=password_mysql_anda
```

2. **Bersihkan cache konfigurasi:**
```bash
php artisan config:clear
```

3. **Uji login di Postman:**
   - URL: `POST http://127.0.0.1:8000/api/login`
   - Body (JSON):
   ```json
   {
       "email": "user1@mail.com",
       "kata_sandi": "password123"
   }
   ```

## 🔑 Akun Pengujian

Setelah memperbaiki password, Anda dapat login dengan:
- Email: `user1@mail.com` sampai `user100@mail.com`
- Password: `password123`

Atau akun admin:
- Email: `basidut@jokowi.com`
- Password: `password123`
