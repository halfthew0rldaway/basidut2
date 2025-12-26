# Paket Pengujian Basidut API - README Lengkap

## 📦 File yang Dibuat

### Postman Collection
**File**: `docs/Basidut_API_Collection.postman_collection.json`

Impor file ini ke Postman untuk mendapatkan semua request pengujian siap pakai!

### Dokumentasi
1. **POSTMAN_IMPORT_GUIDE.md** - Cara impor dan menggunakan collection
2. **API_TESTING_GUIDE.md** - Skenario pengujian detail
3. **API_DOCUMENTATION.md** - Referensi API lengkap
4. **MIGRATION_GUIDE.md** - Instruksi setup database

## 🚀 Memulai dengan Cepat

### 1. Impor ke Postman
```
File → Import → Upload Files → Pilih Basidut_API_Collection.postman_collection.json
```

### 2. Jalankan Pengujian Sesuai Urutan

Collection mencakup **15 request** yang diorganisir dalam 5 folder:

#### 1️⃣ Autentikasi (4 request)
- Register Pengguna Baru
- **Login - Dapatkan Token JWT** (menyimpan token otomatis!)
- Dapatkan Profil Pengguna Saat Ini
- Logout

#### 2️⃣ Produk - CRUD (5 request)
- Dapatkan Semua Produk
- Dapatkan Produk Tunggal
- Buat Produk (menguji constraints)
- Perbarui Produk
- Hapus Produk

#### 3️⃣ Pesanan - Stored Procedure (4 request)
- **Buat Pesanan** - Menguji stored procedure dengan transaksi ACID
- Buat Pesanan - Uji validasi stok (seharusnya gagal)
- Dapatkan Pesanan Pengguna (query JOIN)
- Dapatkan Detail Pesanan Tunggal

#### 4️⃣ Fitur Advanced (2 request)
- **Monitoring Pengiriman** - Menguji database VIEW
- **Log Audit** - Menguji database TRIGGER

#### 5️⃣ Health Check (1 request)
- Pemeriksaan Kesehatan API

## ✅ Yang Diuji

### Fitur Database (Requirement TB)
- ✅ **Stored Procedure**: `sp_buat_pesanan_enterprise`
- ✅ **Trigger**: `trg_audit_stok_update`
- ✅ **Function**: `hitung_total_pesanan` (digunakan secara internal)
- ✅ **View**: `v_monitoring_pengiriman`
- ✅ **Transaction**: BEGIN/COMMIT/ROLLBACK
- ✅ **Query JOIN**: Multi-table joins
- ✅ **Constraints**: CHECK, FOREIGN KEY, UNIQUE
- ✅ **Indexes**: Optimasi performa

### Fitur API
- ✅ Autentikasi JWT
- ✅ Operasi CRUD (3 modul: Produk, Pesanan, Pengguna)
- ✅ Endpoint Terproteksi
- ✅ Validasi Request
- ✅ Penanganan Error

## 📋 Daftar Periksa Pengujian

Sebelum pengujian:
- [ ] Jalankan `php artisan migrate:fresh --seed`
- [ ] Jalankan server: `php artisan serve`
- [ ] Impor Postman collection

Urutan pengujian:
1. [ ] Login (menyimpan token otomatis)
2. [ ] Dapatkan produk
3. [ ] Buat produk
4. [ ] Buat pesanan (menguji stored procedure)
5. [ ] Periksa log audit (menguji trigger)
6. [ ] Periksa monitoring pengiriman (menguji view)

## 🎯 Pengujian Kunci untuk TB

### Pengujian 1: Stored Procedure + Transaction
**Request**: `3.1 Create Order - Test Stored Procedure`

Menguji:
- Transaksi ACID (BEGIN/COMMIT)
- Row locking (FOR UPDATE)
- Multi-table insert
- Validasi stok
- Penanganan error dengan ROLLBACK

### Pengujian 2: Trigger
**Request**: `4.2 Audit Logs (Trigger)`

Setelah membuat pesanan, ini menampilkan:
- Logging audit otomatis
- Pelacakan perubahan stok
- Bukti eksekusi trigger

### Pengujian 3: View
**Request**: `4.1 Shipping Monitoring (View)`

Menampilkan:
- Penggunaan database view
- Multi-table JOIN
- Agregasi data real-time

### Pengujian 4: Query JOIN
**Request**: `3.3 Get User's Orders`

Menggunakan JOIN kompleks:
```sql
pesanan 
  LEFT JOIN pengiriman
  LEFT JOIN item_pesanan
  LEFT JOIN produk
```

## 📊 Hasil yang Diharapkan

Setelah menjalankan semua pengujian:

**Perubahan Database:**
- Produk baru dibuat (ID: 4)
- Pesanan baru dibuat
- Stok berkurang (mis., Laptop Pro: 50 → 48)
- Entri log audit (2-3 entri)

**Respons API:**
- Semua request mengembalikan JSON yang benar
- Token JWT tersimpan otomatis
- Endpoint terproteksi berfungsi dengan token
- Error validasi ditangani dengan baik

## 🔍 Verifikasi di Database

```sql
-- Periksa pesanan dibuat
SELECT * FROM pesanan ORDER BY id DESC LIMIT 1;

-- Periksa stok berkurang
SELECT nama, stok FROM produk WHERE id = 1;

-- Periksa log audit (bukti trigger)
SELECT * FROM log_audit ORDER BY id DESC;

-- Uji view
SELECT * FROM v_monitoring_pengiriman;

-- Uji function
SELECT hitung_total_pesanan(1);
```

## 📝 Catatan

- **Penyimpanan Token Otomatis**: Request login secara otomatis menyimpan token JWT
- **Urutan Pengujian**: Jalankan request secara berurutan untuk hasil terbaik
- **Pelacakan Stok**: Setiap pesanan mengurangi stok
- **Log Audit**: Trigger membuat entri secara otomatis

## 🎓 Untuk Presentasi TB

Anda dapat mendemonstrasikan:
1. **Stored Procedure** - Tampilkan pembuatan pesanan di Postman
2. **Trigger** - Tampilkan log audit terisi otomatis
3. **View** - Tampilkan data monitoring pengiriman
4. **Function** - Jalankan di database: `SELECT hitung_total_pesanan(1)`
5. **Transaction** - Tampilkan rollback pada kegagalan validasi stok
6. **JOIN** - Tampilkan hasil query kompleks di endpoint pesanan

Semua fitur advanced database berfungsi dan dapat diuji via API! 🚀
