# Basidut - Sistem E-Commerce Enterprise

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
</p>

## 📖 Tentang Proyek

**Basidut** adalah sistem e-commerce enterprise yang dibangun dengan arsitektur **Monolithic Database** menggunakan Laravel dan MySQL. Proyek ini dibuat sebagai Tugas Besar mata kuliah Basis Data Lanjut, dengan fokus pada implementasi fitur-fitur database advanced seperti Stored Procedure, Trigger, dan View.

### 🎯 Tujuan Pembelajaran

- Implementasi **Stored Procedure** untuk business logic di database
- Penggunaan **Database Trigger** untuk audit logging otomatis
- Penerapan **Database View** untuk query kompleks
- Transaksi **ACID** dengan row locking
- Custom authentication dengan skema Indonesia

---

## ✨ Fitur Utama

### 🔐 Autentikasi Custom
- Tabel `pengguna` dengan field Indonesia (`kata_sandi` bukan `password`)
- Login dan registrasi dengan Laravel Auth
- Session management

### 🛍️ Manajemen Produk
- Katalog produk dengan stok real-time
- Validasi constraint di database level
- Format harga Rupiah

### 📦 Pemesanan dengan Stored Procedure
- **Stored Procedure:** `sp_buat_pesanan_enterprise`
- Transaksi ACID dengan row locking
- Validasi stok otomatis
- Integrasi logistik dalam satu transaksi

### 📊 Audit Logging Otomatis
- **Trigger:** Mencatat setiap perubahan stok
- Tabel `log_audit` untuk tracking
- Timestamp otomatis

### 🚚 Monitoring Pengiriman
- **View:** `v_monitoring_pengiriman`
- Data real-time status pesanan
- Informasi kurir dan nomor resi

---

## 🚀 Cara Install

### Prasyarat

- PHP >= 8.2
- Composer
- MySQL >= 8.0
- HeidiSQL atau MySQL Workbench

### Langkah 1: Clone Repository

```bash
git clone <repository-url>
cd basidut
```

### Langkah 2: Install Dependencies

```bash
composer install
npm install
```

### Langkah 3: Konfigurasi Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit file `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=basidut
DB_USERNAME=root
DB_PASSWORD=your_password
```

### Langkah 4: Setup Database

#### Opsi A: Menggunakan HeidiSQL (Recommended)

1. Buka HeidiSQL
2. Buat koneksi baru ke MySQL server Anda
3. Buka file `database/basidut_schema.sql` (jika ada) atau jalankan script SQL dari `REQUIREMENTS.md`
4. Execute script untuk membuat:
   - Database `basidut`
   - Semua tabel (pengguna, produk, pesanan, dll)
   - Stored Procedure `sp_buat_pesanan_enterprise`
   - Trigger untuk audit logging
   - View `v_monitoring_pengiriman`
   - Dummy data (100 users + 3 products)

#### Opsi B: Menggunakan Command Line

```bash
mysql -u root -p < database/basidut_schema.sql
```

### Langkah 5: Jalankan Migrasi Laravel

```bash
php artisan migrate
```

> **Note:** Migrasi ini hanya untuk tabel Laravel (sessions, cache, jobs). Tabel utama sudah dibuat via SQL script.

### Langkah 6: Jalankan Development Server

```bash
php artisan serve
```

Buka browser dan akses: **http://127.0.0.1:8000**

---

## 📚 Panduan Penggunaan

### Akun Testing

Sistem sudah dilengkapi dengan **100 akun dummy** untuk testing:

| Username | Email | Password |
|----------|-------|----------|
| user1 - user100 | user1@mail.com - user100@mail.com | password123 |

**Contoh Login:**
- Email: `user1@mail.com`
- Password: `password123`

### Produk Tersedia

1. **Laptop Pro** - Rp 15.000.000 (Stok: 50)
2. **Smartphone X** - Rp 8.000.000 (Stok: 100)
3. **Kemeja Kantor** - Rp 150.000 (Stok: 200)

### Flow Testing

1. **Register/Login** → Buat akun baru atau gunakan akun dummy
2. **Browse Produk** → Lihat katalog di halaman `/shop`
3. **Buat Pesanan** → Klik "Beli Sekarang", isi form, submit
4. **Lihat Pesanan** → Cek history di menu "Pesanan"
5. **Verifikasi Database** → Cek di HeidiSQL untuk melihat:
   - Pesanan baru di tabel `pesanan`
   - Item pesanan di `item_pesanan`
   - Stok berkurang di `produk`
   - Log audit di `log_audit`

---

## 🏗️ Arsitektur

### Database Schema

```
pengguna (Users)
├── id
├── username
├── email
├── kata_sandi (hashed password)
├── nama_lengkap
└── aktif

produk (Products)
├── id
├── nama
├── harga (CHECK >= 0)
├── sku
├── stok (CHECK >= 0)
└── kategori_id

pesanan (Orders)
├── id
├── nomor_pesanan
├── pelanggan_id → pengguna.id
├── total
└── status (ENUM)

item_pesanan (Order Items)
├── pesanan_id → pesanan.id
├── produk_id → produk.id
├── jumlah
└── harga_satuan

pengiriman (Shipping)
├── pesanan_id → pesanan.id
├── kurir
├── nomor_resi
└── status_pengiriman
```

### Stored Procedure

```sql
CALL sp_buat_pesanan_enterprise(
    user_id INT,
    product_id INT,
    qty INT,
    courier VARCHAR(50),
    address TEXT,
    OUT order_id INT,
    OUT status VARCHAR(100)
);
```

**Fungsi:**
- Validasi stok produk
- Lock row untuk concurrency control
- Insert ke `pesanan`, `item_pesanan`, `pengiriman`
- Kurangi stok produk
- COMMIT atau ROLLBACK otomatis

### Trigger

```sql
CREATE TRIGGER trg_audit_stok_update
AFTER UPDATE ON produk
FOR EACH ROW
BEGIN
    IF OLD.stok <> NEW.stok THEN
        INSERT INTO log_audit (...)
        VALUES (...);
    END IF;
END;
```

---
🚀 Langkah Instalasi JWT
1. Install Package JWT
Jalankan perintah berikut untuk mengunduh package:
```bash
composer require tymon/jwt-auth
```
2. Publish Konfigurasi
Publish file konfigurasi agar kamu bisa menyesuaikan pengaturan JWT:
```bash
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
```
3. Generate Secret Key
Buat kunci rahasia yang digunakan untuk mengenkripsi token:
```bash
php artisan jwt:secret
```

🛠️ Konfigurasi Backend
1. Setup API (Laravel 11+)
Pastikan struktur API sudah terpasang di Laravel kamu:
```bash
php artisan install:api
```

2. Konfigurasi Auth Guard
Buka file config/auth.php dan tambahkan guard api agar menggunakan driver jwt:
```bash
'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
        'api' => [
        'driver' => 'jwt',
        'provider' => 'users',
    ],
    ],
```
3. Bypass CSRF untuk API
Untuk menghindari error 419 Page Expired saat melakukan request POST ke API, kita perlu mengecualikan rute API dari pengecekan CSRF di bootstrap/app.php:
```bash
    ->withMiddleware(function (Middleware $middleware): void {
     $middleware->validateCsrfTokens(except: [
        'api/*', // Semua rute yang diawali api/ akan bebas dari CSRF
        'register' 
    ]);
```

📁 Struktur Controller
Berikut adalah perintah untuk membuat controller yang dibutuhkan:

Register (Invokable): php artisan make:controller Api/RegisterController -i

Login (Invokable): php artisan make:controller Api/LoginController -i

Produk: php artisan make:controller Api/ProdukController

🔒 Implementasi Pengguna Model
Pastikan model Pengguna.php mengimplementasikan Tymon\JWTAuth\Contracts\JWTSubject:
```bash
public function getJWTIdentifier() {
        return $this->getKey();
    }

    public function getJWTCustomClaims() {
        return [];
    }
```


## 🛠️ Teknologi

- **Backend:** Laravel 11
- **Database:** MySQL 8.0
- **Frontend:** Blade Templates + Bootstrap 5
- **Font:** Inter (Google Fonts)
- **Icons:** Bootstrap Icons

---

## 📁 Struktur Folder

```
basidut/
├── app/
│   ├── Http/Controllers/
│   │   ├── AuthController.php
│   │   ├── ProdukController.php
│   │   └── PesananController.php
│   └── Models/
│       ├── Pengguna.php
│       ├── Produk.php
│       ├── Pesanan.php
│       ├── ItemPesanan.php
│       └── Pengiriman.php
├── resources/views/
│   ├── layouts/app.blade.php
│   ├── auth/
│   ├── shop.blade.php
│   ├── orders/
│   └── guide.blade.php
├── routes/web.php
├── database/
│   └── basidut_schema.sql
└── REQUIREMENTS.md
```

---

## 📖 Dokumentasi Tambahan

- **[REQUIREMENTS.md](REQUIREMENTS.md)** - Spesifikasi teknis dan requirements
- **[/guide](http://127.0.0.1:8000/guide)** - Panduan testing di aplikasi

---

## 🧪 Testing

### Manual Testing

1. Akses `/guide` untuk panduan lengkap
2. Test autentikasi (register/login)
3. Test product listing
4. Test order creation (stored procedure)
5. Test order history
6. Verifikasi di HeidiSQL

### Database Verification

```sql
-- Cek pesanan terbaru
SELECT * FROM pesanan ORDER BY id DESC LIMIT 5;

-- Cek stok produk
SELECT nama, stok FROM produk;

-- Cek audit log
SELECT * FROM log_audit ORDER BY id DESC LIMIT 10;

-- Cek monitoring pengiriman
SELECT * FROM v_monitoring_pengiriman;
```

---

## 👥 Tim Pengembang

Tugas Besar Basis Data Lanjut - [Nama Universitas]

---

## 📄 Lisensi

Proyek ini dibuat untuk keperluan akademik.

---

## 🙏 Acknowledgments

- Laravel Framework
- Bootstrap 5
- MySQL Documentation
- HeidiSQL

---

**Happy Coding! 🚀**