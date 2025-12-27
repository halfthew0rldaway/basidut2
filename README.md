# Basidut - Sistem E-Commerce Enterprise

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/JWT-Auth-000000?style=for-the-badge&logo=json-web-tokens&logoColor=white" alt="JWT">
</p>

## 📖 Tentang Proyek

**Basidut** adalah sistem e-commerce enterprise yang dibangun untuk **Tugas Besar Basis Data Lanjut**. Proyek ini mengimplementasikan fitur-fitur advanced database seperti Stored Procedure, Trigger, Function, View, dan Transaction ACID dengan fokus pada **API backend** yang dapat diuji melalui Postman.

### 🎯 Fokus Implementasi

✅ **Database Schema** - 10+ entitas dengan normalisasi 3NF  
✅ **Advanced Features** - Stored Procedure, Trigger, Function, View  
✅ **REST API** - 15 endpoints dengan JWT authentication  
✅ **Performance Testing** - 1000+ rows untuk optimasi query  
✅ **Backup Strategy** - mysqldump dengan automasi  

---

## 🚀 Quick Start

### 1. Install Dependencies
```bash
composer install
npm install
```

### 2. Setup Environment
```bash
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
```

Edit `.env`:
```env
DB_CONNECTION=mysql
DB_DATABASE=basidut
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 3. Run Migration & Seeding
```bash
php artisan migrate:fresh --seed
```

### 4. Start Server
```bash
php artisan serve
```

### 5. Test API
Import `docs/Basidut_API_Collection.postman_collection.json` ke Postman dan mulai testing!

---

## 📚 Dokumentasi Lengkap

### 📋 Getting Started
- **[MIGRATION_GUIDE.md](docs/MIGRATION_GUIDE.md)** - Setup database & migration
- **[DATABASE_SETUP.md](docs/DATABASE_SETUP.md)** - Database configuration

### 🔌 API Documentation
- **[API_DOCUMENTATION.md](docs/API_DOCUMENTATION.md)** - Complete API reference dengan JWT details
- **[API_TESTING_GUIDE.md](docs/API_TESTING_GUIDE.md)** - Testing scenarios
- **[API_QUICK_REFERENCE.md](docs/API_QUICK_REFERENCE.md)** - Quick reference
- **[POSTMAN_IMPORT_GUIDE.md](docs/POSTMAN_IMPORT_GUIDE.md)** - Cara import & test Postman

### 🎯 Testing & Performance
- **[PERFORMANCE_TESTING.md](docs/PERFORMANCE_TESTING.md)** - Query optimization & 1000+ rows
- **[TESTING_PACKAGE_README.md](docs/TESTING_PACKAGE_README.md)** - Complete testing package

### 💾 Backup & Maintenance
- **[BACKUP_STRATEGY.md](docs/BACKUP_STRATEGY.md)** - mysqldump strategy & automation

### ✅ Checklist
- **[TB_CHECKLIST.md](.gemini/antigravity/brain/d2e08e1d-a819-479e-909b-fb69e8b8667f/TB_CHECKLIST.md)** - Verifikasi semua requirement TB

---

## ✨ Fitur Utama

### 🔐 Autentikasi & Keamanan
- **JWT Authentication** dengan algoritma HS256 (HMAC-SHA256)
- **Bcrypt Password Hashing** (12 rounds)
- **Protected API Endpoints** dengan middleware
- **Token Expiration** otomatis

### 🗄️ Advanced Database Features

#### 1. Stored Procedure
```sql
CALL sp_buat_pesanan_enterprise(user_id, product_id, qty, courier, address, @order_id, @status);
```
- ✅ ACID Transaction (BEGIN/COMMIT/ROLLBACK)
- ✅ Row Locking (FOR UPDATE)
- ✅ Stock Validation
- ✅ Multi-table Insert

#### 2. Trigger
```sql
-- 3 Triggers untuk audit logging lengkap:
trg_audit_stok_update      -- Mencatat UPDATE stok
trg_audit_produk_insert    -- Mencatat INSERT produk baru
trg_audit_produk_delete    -- Mencatat DELETE produk
```

#### 3. Function
```sql
SELECT hitung_total_pesanan(1) as total;
```

#### 4. View
```sql
SELECT * FROM v_monitoring_pengiriman;
```

### 📊 Database Schema

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
                   │
                   ↓
              log_audit (audit trail)
```

**8 Main Tables:**
- `kategori` - Product categories
- `pengguna` - Users (bcrypt passwords)
- `produk` - Products with constraints
- `pesanan` - Orders
- `item_pesanan` - Order items
- `pengiriman` - Shipping
- `log_audit` - Audit logs
- `metode_pembayaran` - Payment methods

---

## 🔌 API Endpoints (15 Total)

### Public Endpoints (5)
- `POST /api/register` - Register user
- `POST /api/login` - Login & get JWT token
- `GET /api/produk` - List products
- `GET /api/produk/{id}` - Product details
- `GET /api/health` - Health check

### Protected Endpoints (10) - Requires JWT
- `GET /api/me` - User profile
- `POST /api/logout` - Logout
- `POST /api/produk` - Create product
- `PUT /api/produk/{id}` - Update product
- `DELETE /api/produk/{id}` - Delete product
- `GET /api/pesanan` - User's orders
- `GET /api/pesanan/{id}` - Order details
- `POST /api/pesanan` - Create order (stored procedure)
- `GET /api/monitoring-pengiriman` - Shipping monitoring (view)
- `GET /api/audit-logs` - Audit logs (trigger)

**Lihat:** [API_DOCUMENTATION.md](docs/API_DOCUMENTATION.md) untuk detail lengkap

---

## 🧪 Testing

### Import Postman Collection
```bash
File → Import → docs/Basidut_API_Collection.postman_collection.json
```

### Test Credentials
- Email: `user1@mail.com` to `user100@mail.com`
- Password: `password123`

### Quick Test
```bash
# Login
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user1@mail.com","kata_sandi":"password123"}'

# Get products
curl http://127.0.0.1:8000/api/produk
```

**Lihat:** [POSTMAN_IMPORT_GUIDE.md](docs/POSTMAN_IMPORT_GUIDE.md) untuk panduan lengkap

---

## 📈 Performance Testing

### Seed 1000+ Rows
```bash
php artisan db:seed --class=PerformanceTestSeeder
```

**Creates:**
- 1000 products
- 500 orders
- 1500+ order items
- **Total: 3000+ rows**

**Lihat:** [PERFORMANCE_TESTING.md](docs/PERFORMANCE_TESTING.md) untuk query optimization

---

## 💾 Backup & Restore

### Full Backup
```bash
mysqldump -u root -p --routines --triggers basidut > backup/basidut_backup.sql
```

### Restore
```bash
mysql -u root -p basidut < backup/basidut_backup.sql
```

**Lihat:** [BACKUP_STRATEGY.md](docs/BACKUP_STRATEGY.md) untuk strategi lengkap

---

## 🛠️ Teknologi

- **Backend:** Laravel 11
- **Database:** MySQL 8.0
- **Authentication:** JWT (tymon/jwt-auth) - HS256 algorithm
- **Password:** Bcrypt (12 rounds)
- **API:** RESTful JSON
- **Testing:** Postman

---

## 📁 Struktur Proyek

```
basidut/
├── app/
│   ├── Http/Controllers/Api/    # API Controllers
│   └── Models/                   # Eloquent Models
├── database/
│   ├── migrations/               # 9 migration files
│   └── seeders/                  # 5 seeder files
├── routes/
│   ├── api.php                   # 15 API endpoints
│   └── web.php                   # Web routes
├── docs/                         # 📚 Complete documentation
│   ├── API_DOCUMENTATION.md
│   ├── MIGRATION_GUIDE.md
│   ├── PERFORMANCE_TESTING.md
│   ├── BACKUP_STRATEGY.md
│   └── Basidut_API_Collection.postman_collection.json
└── README.md                     # This file
```

---

## ✅ TB Requirements Checklist

### Database
- ✅ 10+ entitas (12 tables)
- ✅ Relasi 1-1, 1-N, N-N
- ✅ Normalisasi 3NF
- ✅ Primary Key, Foreign Key, Unique, Index, CHECK

### Advanced Features
- ✅ Stored Procedure (`sp_buat_pesanan_enterprise`)
- ✅ Function (`hitung_total_pesanan`)
- ✅ Triggers (3): `trg_audit_stok_update`, `trg_audit_produk_insert`, `trg_audit_produk_delete`
- ✅ View (`v_monitoring_pengiriman`)
- ✅ Transaction (ACID)

### API & Testing
- ✅ 3+ modul CRUD
- ✅ JOIN & Subquery
- ✅ Testable via Postman
- ✅ 1000+ rows performance data

### Security & Backup
- ✅ JWT Authentication (HS256)
- ✅ Bcrypt Password Hashing
- ✅ mysqldump Backup Strategy
- ✅ Audit Logging

**Lihat:** [TB_CHECKLIST.md](.gemini/antigravity/brain/d2e08e1d-a819-479e-909b-fb69e8b8667f/TB_CHECKLIST.md) untuk detail lengkap

---

## 🎓 Untuk Presentasi TB

### Demo Flow
1. **Database Schema** - Show ERD & migrations
2. **Advanced Features** - Demo stored procedure, trigger, view
3. **API Testing** - Live demo via Postman
4. **Performance** - Show query optimization with 1000+ rows
5. **Backup** - Demo backup/restore strategy

### Key Points
- ✅ Complete CRUD API dengan JWT
- ✅ Stored procedure dengan ACID transaction
- ✅ Automatic audit logging via trigger
- ✅ Real-time monitoring via view
- ✅ Query optimization dengan indexing
- ✅ Comprehensive backup strategy

---

## 📞 Support

Untuk pertanyaan atau issue, lihat dokumentasi di folder `docs/` atau check:
- [API Documentation](docs/API_DOCUMENTATION.md)
- [Migration Guide](docs/MIGRATION_GUIDE.md)
- [Testing Guide](docs/POSTMAN_IMPORT_GUIDE.md)

---

**Tugas Besar Basis Data Lanjut - Ready for Presentation! 🚀**


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