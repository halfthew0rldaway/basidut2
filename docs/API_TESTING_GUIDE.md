# Panduan Pengujian API - Postman Collection

## 🎯 Ringkasan
Panduan ini menyediakan skenario pengujian API lengkap untuk sistem e-commerce Basidut, dengan fokus pada fitur database dan endpoint API.

## 📦 Postman Collection

### Base URL
```
http://127.0.0.1:8000/api
```

---

## 1️⃣ Pengujian Autentikasi

### 1.1 Registrasi Pengguna Baru
**POST** `/api/register`

**Body (JSON):**
```json
{
    "username": "testuser",
    "email": "testuser@example.com",
    "kata_sandi": "password123",
    "nama_lengkap": "Test User"
}
```

**Respons yang Diharapkan (201):**
```json
{
    "success": true,
    "message": "Registrasi Berhasil",
    "user": {
        "id": 102,
        "username": "testuser",
        "email": "testuser@example.com",
        "nama_lengkap": "Test User",
        "aktif": true
    }
}
```

---

### 1.2 Login (Dapatkan Token JWT)
**POST** `/api/login`

**Body (JSON):**
```json
{
    "email": "user1@mail.com",
    "kata_sandi": "password123"
}
```

**Respons yang Diharapkan (200):**
```json
{
    "success": true,
    "user": {
        "id": 1,
        "username": "user1",
        "email": "user1@mail.com",
        "nama_lengkap": "Pengguna 1"
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
}
```

**⚠️ PENTING:** Simpan nilai `token` untuk digunakan di endpoint terproteksi!

---

### 1.3 Dapatkan Profil Pengguna Saat Ini
**GET** `/api/me`

**Headers:**
```
Authorization: Bearer {TOKEN_ANDA_DI_SINI}
```

**Respons yang Diharapkan (200):**
```json
{
    "success": true,
    "user": {
        "id": 1,
        "username": "user1",
        "email": "user1@mail.com",
        "nama_lengkap": "Pengguna 1",
        "aktif": true
    }
}
```

---

## 2️⃣ Pengujian Produk (CRUD)

### 2.1 Dapatkan Semua Produk
**GET** `/api/produk`

**Respons yang Diharapkan (200):**
```json
{
    "success": true,
    "message": "Daftar Data Produk",
    "data": [
        {
            "id": 1,
            "nama": "Laptop Pro",
            "harga": "15000000.00",
            "sku": "LPT-001",
            "stok": 50,
            "kategori_id": 1
        }
    ]
}
```

---

### 2.3 Buat Produk (Terproteksi)
**POST** `/api/produk`

**Headers:**
```
Authorization: Bearer {TOKEN_ANDA_DI_SINI}
Content-Type: application/json
```

**Body (JSON):**
```json
{
    "nama": "Mouse Gaming",
    "harga": 350000,
    "sku": "MSE-001",
    "stok": 75,
    "kategori_id": 1
}
```

**Respons yang Diharapkan (201):**
```json
{
    "success": true,
    "message": "Produk Berhasil Ditambahkan",
    "data": {
        "id": 4,
        "nama": "Mouse Gaming",
        "harga": "350000.00",
        "sku": "MSE-001",
        "stok": 75,
        "kategori_id": 1
    }
}
```

---

## 3️⃣ Pengujian Pesanan (Stored Procedure)

### 3.1 Buat Pesanan - Menguji Stored Procedure
**POST** `/api/pesanan`

**Headers:**
```
Authorization: Bearer {TOKEN_ANDA_DI_SINI}
Content-Type: application/json
```

**Body (JSON):**
```json
{
    "product_id": 1,
    "qty": 2,
    "courier": "JNE",
    "address": "Jl. Sudirman No. 123, Jakarta Pusat"
}
```

**Respons yang Diharapkan (201):**
```json
{
    "success": true,
    "message": "Pesanan berhasil dibuat!",
    "order_id": 1
}
```

**Efek di Database:**
- ✅ Stok berkurang (trigger mencatat ini ke `log_audit`)
- ✅ Pesanan dibuat di tabel `pesanan`
- ✅ Item pesanan dibuat di tabel `item_pesanan`
- ✅ Record pengiriman dibuat di tabel `pengiriman`
- ✅ Transaksi di-commit (ACID)

---

### 3.2 Dapatkan Pesanan Pengguna
**GET** `/api/pesanan`

**Headers:**
```
Authorization: Bearer {TOKEN_ANDA_DI_SINI}
```

**Respons yang Diharapkan (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "nomor_pesanan": "ORD-1735223456",
            "total": "30000000.00",
            "status": "menunggu",
            "tanggal_pesanan": "2025-12-26 14:30:56",
            "kurir": "JNE",
            "nama_produk": "Laptop Pro",
            "jumlah": 2
        }
    ]
}
```

---

## 4️⃣ Pengujian Fitur Advanced

### 4.1 Monitoring Pengiriman (View)
**GET** `/api/monitoring-pengiriman`

**Headers:**
```
Authorization: Bearer {TOKEN_ANDA_DI_SINI}
```

**Respons yang Diharapkan (200):**
```json
{
    "success": true,
    "data": [
        {
            "pesanan_id": 1,
            "nomor_pesanan": "ORD-1735223456",
            "pelanggan_id": 1,
            "total": "30000000.00",
            "status_pesanan": "menunggu",
            "kurir": "JNE",
            "status_pengiriman": "siap_kirim",
            "nama_produk": "Laptop Pro",
            "jumlah": 2
        }
    ]
}
```

**Fitur Database:** Menggunakan view `v_monitoring_pengiriman`

---

### 4.2 Log Audit (Trigger)
**GET** `/api/audit-logs`

**Headers:**
```
Authorization: Bearer {TOKEN_ANDA_DI_SINI}
```

**Respons yang Diharapkan (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "nama_tabel": "produk",
            "id_record": 1,
            "aksi": "UPDATE",
            "keterangan": "Stok berubah dari 50 menjadi 48",
            "user_pelaku": "SYSTEM",
            "waktu": "2025-12-26 14:30:56"
        }
    ]
}
```

**Fitur Database:** Diisi oleh trigger `trg_audit_stok_update`

---

## 5️⃣ Health Check

### 5.1 Kesehatan API
**GET** `/api/health`

**Respons yang Diharapkan (200):**
```json
{
    "status": "ok",
    "timestamp": "2025-12-26T14:30:00.000000Z",
    "service": "Basidut API",
    "version": "1.0.0"
}
```

---

## 🧪 Skenario Pengujian

### Skenario 1: Alur Pesanan Lengkap
1. Login → Dapatkan token
2. Dapatkan produk → Pilih produk
3. Buat pesanan → Stok berkurang, audit tercatat
4. Dapatkan pesanan → Verifikasi pesanan dibuat
5. Periksa log audit → Verifikasi perubahan stok tercatat

### Skenario 2: Validasi Stok
1. Buat pesanan dengan qty > stok
2. Diharapkan: Error "Stok Tidak Mencukupi"
3. Verifikasi: Stok tidak berubah, tidak ada pesanan dibuat

### Skenario 3: Rollback Transaksi
1. Buat pesanan dengan data invalid
2. Diharapkan: Transaksi di-rollback
3. Verifikasi: Tidak ada data parsial di database

---

## 📊 Query Database untuk Verifikasi

Setelah membuat pesanan, verifikasi di database:

```sql
-- Periksa stok berkurang
SELECT id, nama, stok FROM produk WHERE id = 1;

-- Periksa pesanan dibuat
SELECT * FROM pesanan ORDER BY id DESC LIMIT 1;

-- Periksa item pesanan
SELECT * FROM item_pesanan ORDER BY id DESC LIMIT 1;

-- Periksa pengiriman dibuat
SELECT * FROM pengiriman ORDER BY id DESC LIMIT 1;

-- Periksa log audit
SELECT * FROM log_audit ORDER BY id DESC LIMIT 5;

-- Uji function
SELECT hitung_total_pesanan(1) as total;

-- Uji view
SELECT * FROM v_monitoring_pengiriman;
```

---

## ✅ Kriteria Keberhasilan

Semua pengujian harus lulus dengan:
- ✅ Kode status HTTP yang benar
- ✅ Format respons JSON yang benar
- ✅ Data tersimpan di database
- ✅ Stored procedure berhasil dieksekusi
- ✅ Trigger mencatat entri audit
- ✅ Function menghitung dengan benar
- ✅ View mengembalikan data yang di-join
- ✅ Transaksi mempertahankan properti ACID
