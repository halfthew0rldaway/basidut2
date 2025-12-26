# Dokumentasi API Basidut

## Base URL
```
http://127.0.0.1:8000/api
```

## Autentikasi
API ini menggunakan autentikasi **JWT (JSON Web Token)**. Setelah login, Anda akan menerima token yang harus disertakan dalam header `Authorization` untuk endpoint yang terproteksi.

### Konfigurasi JWT
- **Algorithm**: HS256 (HMAC with SHA-256)
- **Token Type**: Bearer
- **Token Lifetime**: Dapat dikonfigurasi (default: 60 menit)
- **Refresh**: Didukung melalui mekanisme refresh token

### Cara Kerja JWT
1. **Login**: Pengguna mengirim kredensial → Server memvalidasi → Mengembalikan token JWT
2. **Request**: Client menyertakan token dalam header `Authorization: Bearer {token}`
3. **Validasi**: Server memverifikasi signature token menggunakan secret key
4. **Response**: Jika valid, memproses request; jika invalid/expired, mengembalikan 401

### Struktur Token
```
eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbGFyYXZlbC5kZXYvYXBpL2xvZ2luIiwiaWF0IjoxNjQ2MzI5MjAwLCJleHAiOjE2NDYzMzI4MDAsIm5iZiI6MTY0NjMyOTIwMCwianRpIjoiVGRqRlhXRjlTcEVNV0lIZiIsInN1YiI6IjEiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.signature
```

**Bagian-bagian:**
- **Header**: Algorithm & tipe token (HS256, JWT)
- **Payload**: Data pengguna (id, email, exp, iat)
- **Signature**: HMAC-SHA256(header + payload + secret)

### Fitur Keamanan
- ✅ **Verifikasi Signature**: Mencegah manipulasi token
- ✅ **Expiration**: Token otomatis expired setelah waktu yang ditentukan
- ✅ **Secret Key**: Disimpan aman di `.env` (JWT_SECRET)
- ✅ **Stateless**: Tidak memerlukan penyimpanan session

### Menggunakan Token
```
Authorization: Bearer {token-jwt-anda}
```

---

## 📋 Daftar Endpoint API

### 🔓 Endpoint Publik (Tidak Memerlukan Autentikasi)

#### 1. Registrasi Pengguna Baru
**POST** `/api/register`

Mendaftarkan akun pengguna baru.

**Request Body:**
```json
{
  "username": "johndoe",
  "email": "john@example.com",
  "kata_sandi": "password123",
  "nama_lengkap": "John Doe"
}
```

**Response (201 Created):**
```json
{
  "success": true,
  "message": "Registrasi Berhasil",
  "user": {
    "id": 101,
    "username": "johndoe",
    "email": "john@example.com",
    "nama_lengkap": "John Doe",
    "aktif": true
  }
}
```

---

#### 2. Login
**POST** `/api/login`

Login dan menerima token JWT.

**Request Body:**
```json
{
  "email": "user1@mail.com",
  "kata_sandi": "password123"
}
```

**Response (200 OK):**
```json
{
  "success": true,
  "user": {
    "id": 1,
    "username": "user1",
    "email": "user1@mail.com",
    "nama_lengkap": "User Satu"
  },
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
}
```

---

#### 3. Dapatkan Semua Produk
**GET** `/api/produk`

Mendapatkan daftar semua produk yang tersedia.

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Daftar Data Produk",
  "data": [
    {
      "id": 1,
      "nama": "Laptop Pro",
      "harga": "15000000.00",
      "sku": "LP-001",
      "stok": 50,
      "kategori_id": 1
    },
    {
      "id": 2,
      "nama": "Smartphone X",
      "harga": "8000000.00",
      "sku": "SP-001",
      "stok": 100,
      "kategori_id": 1
    }
  ]
}
```

---

#### 4. Dapatkan Detail Produk
**GET** `/api/produk/{id}`

Mendapatkan detail produk tertentu.

**Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "nama": "Laptop Pro",
    "harga": "15000000.00",
    "sku": "LP-001",
    "stok": 50,
    "kategori_id": 1
  }
}
```

---

#### 5. Health Check
**GET** `/api/health`

Memeriksa apakah API berjalan dengan baik.

**Response (200 OK):**
```json
{
  "status": "ok",
  "timestamp": "2025-12-26T13:35:00.000000Z",
  "service": "Basidut API",
  "version": "1.0.0"
}
```

---

### 🔒 Endpoint Terproteksi (Memerlukan Autentikasi)

> **Catatan:** Semua endpoint terproteksi memerlukan header `Authorization: Bearer {token}`.

#### 6. Dapatkan Profil Pengguna Saat Ini
**GET** `/api/me`

Mendapatkan informasi profil pengguna yang sedang login.

**Headers:**
```
Authorization: Bearer {token-jwt-anda}
```

**Response (200 OK):**
```json
{
  "success": true,
  "user": {
    "id": 1,
    "username": "user1",
    "email": "user1@mail.com",
    "nama_lengkap": "User Satu",
    "aktif": true
  }
}
```

---

#### 7. Logout
**POST** `/api/logout`

Logout dan membatalkan token JWT saat ini.

**Headers:**
```
Authorization: Bearer {token-jwt-anda}
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Successfully logged out"
}
```

---

#### 8. Buat Produk Baru
**POST** `/api/produk`

Membuat produk baru (khusus Admin).

**Headers:**
```
Authorization: Bearer {token-jwt-anda}
Content-Type: application/json
```

**Request Body:**
```json
{
  "nama": "Produk Baru",
  "harga": 500000,
  "sku": "NP-001",
  "stok": 100,
  "kategori_id": 1
}
```

**Response (201 Created):**
```json
{
  "success": true,
  "message": "Produk Berhasil Ditambahkan",
  "data": {
    "id": 4,
    "nama": "Produk Baru",
    "harga": "500000.00",
    "sku": "NP-001",
    "stok": 100,
    "kategori_id": 1
  }
}
```

---

#### 9. Perbarui Produk
**PUT** `/api/produk/{id}`

Memperbarui produk yang sudah ada.

**Headers:**
```
Authorization: Bearer {token-jwt-anda}
Content-Type: application/json
```

**Request Body:**
```json
{
  "nama": "Nama Produk Diperbarui",
  "harga": 550000,
  "stok": 120
}
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Produk Berhasil Diperbarui",
  "data": {
    "id": 4,
    "nama": "Nama Produk Diperbarui",
    "harga": "550000.00",
    "sku": "NP-001",
    "stok": 120,
    "kategori_id": 1
  }
}
```

---

#### 10. Hapus Produk
**DELETE** `/api/produk/{id}`

Menghapus produk.

**Headers:**
```
Authorization: Bearer {token-jwt-anda}
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Produk Berhasil Dihapus"
}
```

---

#### 11. Dapatkan Riwayat Pesanan Pengguna
**GET** `/api/pesanan`

Mendapatkan semua pesanan untuk pengguna yang sedang login.

**Headers:**
```
Authorization: Bearer {token-jwt-anda}
```

**Response (200 OK):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "nomor_pesanan": "ORD-20251226-001",
      "total": "15000000.00",
      "status": "selesai",
      "tanggal_pesanan": "2025-12-26 10:30:00",
      "kurir": "JNE",
      "nomor_resi": "JNE123456789",
      "status_pengiriman": "terkirim",
      "nama_produk": "Laptop Pro",
      "jumlah": 1,
      "harga_satuan": "15000000.00"
    }
  ]
}
```

---

#### 12. Dapatkan Detail Pesanan
**GET** `/api/pesanan/{id}`

Mendapatkan informasi detail tentang pesanan tertentu.

**Headers:**
```
Authorization: Bearer {token-jwt-anda}
```

**Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "nomor_pesanan": "ORD-20251226-001",
    "pelanggan_id": 1,
    "total": "15000000.00",
    "status": "selesai",
    "tanggal_pesanan": "2025-12-26 10:30:00",
    "item_pesanan": [
      {
        "pesanan_id": 1,
        "produk_id": 1,
        "jumlah": 1,
        "harga_satuan": "15000000.00",
        "produk": {
          "id": 1,
          "nama": "Laptop Pro",
          "harga": "15000000.00",
          "sku": "LP-001",
          "stok": 49
        }
      }
    ],
    "pengiriman": {
      "pesanan_id": 1,
      "kurir": "JNE",
      "nomor_resi": "JNE123456789",
      "alamat_tujuan": "Jl. Contoh No. 123",
      "status_pengiriman": "terkirim"
    }
  }
}
```

---

#### 13. Buat Pesanan Baru
**POST** `/api/pesanan`

Membuat pesanan baru menggunakan stored procedure `sp_buat_pesanan_enterprise`.

**Headers:**
```
Authorization: Bearer {token-jwt-anda}
Content-Type: application/json
```

**Request Body:**
```json
{
  "product_id": 1,
  "qty": 2,
  "courier": "JNE",
  "address": "Jl. Contoh No. 123, Jakarta"
}
```

**Response (201 Created):**
```json
{
  "success": true,
  "message": "Pesanan berhasil dibuat!",
  "order_id": 5
}
```

**Error Response (400 Bad Request):**
```json
{
  "success": false,
  "message": "Stok tidak mencukupi"
}
```

---

#### 14. Dapatkan Monitoring Pengiriman
**GET** `/api/monitoring-pengiriman`

Mendapatkan data monitoring pengiriman menggunakan database view `v_monitoring_pengiriman`.

**Headers:**
```
Authorization: Bearer {token-jwt-anda}
```

**Response (200 OK):**
```json
{
  "success": true,
  "data": [
    {
      "pesanan_id": 1,
      "nomor_pesanan": "ORD-20251226-001",
      "pelanggan_id": 1,
      "total": "15000000.00",
      "status_pesanan": "selesai",
      "kurir": "JNE",
      "nomor_resi": "JNE123456789",
      "status_pengiriman": "terkirim",
      "nama_produk": "Laptop Pro",
      "jumlah": 1
    }
  ]
}
```

---

#### 15. Dapatkan Log Audit
**GET** `/api/audit-logs`

Mendapatkan log audit untuk perubahan stok (khusus Admin - pertimbangkan menambahkan middleware admin).

**Headers:**
```
Authorization: Bearer {token-jwt-anda}
```

**Response (200 OK):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "produk_id": 1,
      "stok_lama": 50,
      "stok_baru": 49,
      "perubahan": -1,
      "keterangan": "Pengurangan stok dari pesanan #1",
      "waktu": "2025-12-26 10:30:00"
    }
  ]
}
```

---

## 🔧 Menguji API

### Menggunakan cURL

#### 1. Registrasi
```bash
curl -X POST http://127.0.0.1:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "username": "testuser",
    "email": "test@example.com",
    "kata_sandi": "password123",
    "nama_lengkap": "Test User"
  }'
```

#### 2. Login
```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user1@mail.com",
    "kata_sandi": "password123"
  }'
```

#### 3. Dapatkan Produk (Publik)
```bash
curl http://127.0.0.1:8000/api/produk
```

#### 4. Dapatkan Profil Pengguna (Terproteksi)
```bash
curl http://127.0.0.1:8000/api/me \
  -H "Authorization: Bearer TOKEN_JWT_ANDA_DI_SINI"
```

#### 5. Buat Pesanan (Terproteksi)
```bash
curl -X POST http://127.0.0.1:8000/api/pesanan \
  -H "Authorization: Bearer TOKEN_JWT_ANDA_DI_SINI" \
  -H "Content-Type: application/json" \
  -d '{
    "product_id": 1,
    "qty": 1,
    "courier": "JNE",
    "address": "Jl. Contoh No. 123"
  }'
```

---

## 📝 Response Error

### 401 Unauthorized
```json
{
  "success": false,
  "message": "Email atau Kata Sandi Anda salah"
}
```

### 404 Not Found
```json
{
  "success": false,
  "message": "Pesanan tidak ditemukan"
}
```

### 422 Validation Error
```json
{
  "email": ["The email field is required."],
  "kata_sandi": ["The kata sandi field is required."]
}
```

### 500 Internal Server Error
```json
{
  "success": false,
  "message": "Error: Database connection failed"
}
```

---

## 🚀 Memulai dengan Cepat

1. **Jalankan server Laravel:**
   ```bash
   php artisan serve
   ```

2. **Uji endpoint health:**
   ```bash
   curl http://127.0.0.1:8000/api/health
   ```

3. **Login dengan akun pengujian:**
   ```bash
   curl -X POST http://127.0.0.1:8000/api/login \
     -H "Content-Type: application/json" \
     -d '{"email": "user1@mail.com", "kata_sandi": "password123"}'
   ```

4. **Gunakan token yang dikembalikan untuk request yang terautentikasi!**

---

## 📚 Catatan Tambahan

- Semua timestamp dalam format ISO 8601
- Semua nilai moneter dalam Rupiah Indonesia (IDR)
- API menggunakan stored procedure `sp_buat_pesanan_enterprise` untuk pembuatan pesanan
- Perubahan stok dicatat secara otomatis melalui database trigger
- View `v_monitoring_pengiriman` menyediakan monitoring pengiriman real-time
