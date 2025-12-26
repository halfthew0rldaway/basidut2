# Panduan Migrasi Database - Basidut

## 🚀 Memulai dengan Cepat

### Langkah 1: Hapus Database Lama (jika ada)
```sql
DROP DATABASE IF EXISTS basidut;
CREATE DATABASE basidut CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Atau menggunakan HeidiSQL:
- Klik kanan pada database `basidut` → Drop
- Buat database baru dengan nama `basidut`

### Langkah 2: Perbarui File .env
Pastikan file `.env` memiliki pengaturan database yang benar:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=basidut
DB_USERNAME=root
DB_PASSWORD=password_mysql_anda

JWT_SECRET=kunci_jwt_anda_di_sini
```

### Langkah 3: Jalankan Migrasi Fresh dengan Seeding
```bash
php artisan migrate:fresh --seed
```

Satu perintah ini akan:
- Menghapus semua tabel yang ada
- Menjalankan semua migrasi (membuat tabel, stored procedure, trigger, function, view)
- Melakukan seeding database dengan data pengujian

### Langkah 4: Verifikasi Pengaturan
```bash
php artisan tinker
```

Kemudian jalankan pemeriksaan berikut:
```php
// Periksa jumlah pengguna (seharusnya 101)
App\Models\Pengguna::count();

// Periksa password di-hash dengan bcrypt
App\Models\Pengguna::first()->kata_sandi; // Seharusnya dimulai dengan $2y$

// Periksa produk
App\Models\Produk::count(); // Seharusnya 3

// Periksa stored procedure ada
DB::select("SHOW PROCEDURE STATUS WHERE Db = 'basidut'");

// Periksa trigger ada
DB::select("SHOW TRIGGERS WHERE `Table` = 'produk'");

// Periksa view ada
DB::select("SHOW FULL TABLES WHERE Table_Type = 'VIEW'");
```

## 📋 Yang Akan Dibuat

### Tabel (8)
1. **kategori** - Kategori produk
2. **pengguna** - Pengguna dengan password bcrypt
3. **produk** - Produk dengan foreign key ke kategori
4. **pesanan** - Pesanan dengan foreign key ke pengguna
5. **item_pesanan** - Item pesanan (relasi many-to-many)
6. **pengiriman** - Informasi pengiriman
7. **log_audit** - Log audit
8. **metode_pembayaran** - Metode pembayaran

### Fitur Advanced (4)
1. **Stored Procedure**: `sp_buat_pesanan_enterprise` - Membuat pesanan dengan transaksi ACID
2. **Trigger**: `trg_audit_stok_update` - Mencatat perubahan stok secara otomatis
3. **Function**: `hitung_total_pesanan` - Menghitung total pesanan
4. **View**: `v_monitoring_pengiriman` - Monitoring pengiriman

### Data Seeding
- **101 pengguna** (user1-user100 + admin) - Password: `password123` (di-hash dengan bcrypt)
- **3 kategori** (Elektronik, Fashion, Rumah Tangga)
- **3 produk** (Laptop Pro, Smartphone X, Kemeja Kantor)
- **2 metode pembayaran** (Transfer Bank, Kartu Kredit)

## 🧪 Menguji Pengaturan

### Pengujian 1: Login API
```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user1@mail.com","kata_sandi":"password123"}'
```

Hasil yang diharapkan: Token JWT dikembalikan

### Pengujian 2: Ambil Produk
```bash
curl http://127.0.0.1:8000/api/produk
```

Hasil yang diharapkan: 3 produk dikembalikan

### Pengujian 3: Buat Pesanan (Menguji Stored Procedure)
Login terlebih dahulu untuk mendapatkan token, kemudian:
```bash
curl -X POST http://127.0.0.1:8000/api/pesanan \
  -H "Authorization: Bearer TOKEN_ANDA" \
  -H "Content-Type: application/json" \
  -d '{"product_id":1,"qty":1,"courier":"JNE","address":"Alamat Pengujian"}'
```

Hasil yang diharapkan: Pesanan berhasil dibuat

### Pengujian 4: Periksa Log Audit (Menguji Trigger)
```bash
php artisan tinker
```
```php
DB::table('log_audit')->get();
```

Hasil yang diharapkan: Entri audit untuk perubahan stok

### Pengujian 5: Uji Function
```bash
php artisan tinker
```
```php
DB::select("SELECT hitung_total_pesanan(1) as total");
```

### Pengujian 6: Uji View
```bash
php artisan tinker
```
```php
DB::table('v_monitoring_pengiriman')->get();
```

## 🔄 Perintah Umum

### Migrasi Fresh (Menghapus Semua)
```bash
php artisan migrate:fresh --seed
```

### Rollback Migrasi Terakhir
```bash
php artisan migrate:rollback
```

### Periksa Status Migrasi
```bash
php artisan migrate:status
```

### Jalankan Seeder Saja
```bash
php artisan db:seed
```

### Jalankan Seeder Tertentu
```bash
php artisan db:seed --class=PenggunaSeeder
```

## 🔑 Kredensial Pengujian

**Pengguna Reguler:**
- Email: `user1@mail.com` sampai `user100@mail.com`
- Password: `password123`

**Admin:**
- Email: `basidut@jokowi.com`
- Password: `password123`

## ⚠️ Pemecahan Masalah

### Error: "Access denied for user"
- Periksa DB_USERNAME dan DB_PASSWORD di `.env`
- Pastikan MySQL sedang berjalan

### Error: "Database doesn't exist"
- Buat database secara manual: `CREATE DATABASE basidut;`

### Error: "Syntax error in migration"
- Pastikan versi MySQL adalah 8.0+
- Periksa bahwa semua migrasi dalam urutan yang benar

### Password tidak berfungsi
- Pastikan seeder berhasil dijalankan
- Periksa password di-hash dengan bcrypt: `App\Models\Pengguna::first()->kata_sandi`
- Seharusnya dimulai dengan `$2y$`

## 📊 Gambaran Skema Database

```
kategori (1) ──< produk (N)
                   │
                   │ (N)
                   ↓
pengguna (1) ──< pesanan (N) ──< item_pesanan (N)
                   │
                   │ (1)
                   ↓
              pengiriman (1)
```

## ✅ Daftar Periksa Keberhasilan

Setelah menjalankan migrasi, verifikasi:
- [ ] Semua 8 tabel dibuat
- [ ] Stored procedure `sp_buat_pesanan_enterprise` ada
- [ ] Trigger `trg_audit_stok_update` ada
- [ ] Function `hitung_total_pesanan` ada
- [ ] View `v_monitoring_pengiriman` ada
- [ ] 101 pengguna di-seed dengan password bcrypt
- [ ] 3 produk di-seed
- [ ] API login berfungsi
- [ ] Dapat membuat pesanan via API
- [ ] Log audit mencatat perubahan stok
