# Basidut API Documentation

## Base URL
```
http://127.0.0.1:8000/api
```

## Authentication
The API uses **JWT (JSON Web Token)** authentication. After logging in, you'll receive a token that must be included in the `Authorization` header for protected endpoints.

```
Authorization: Bearer {your-jwt-token}
```

---

## 📋 API Endpoints

### 🔓 Public Endpoints (No Authentication Required)

#### 1. Register New User
**POST** `/api/register`

Register a new user account.

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

Login and receive a JWT token.

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

#### 3. Get All Products
**GET** `/api/produk`

Get list of all available products.

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

#### 4. Get Single Product
**GET** `/api/produk/{id}`

Get details of a specific product.

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

Check if the API is running.

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

### 🔒 Protected Endpoints (Authentication Required)

> **Note:** All protected endpoints require the `Authorization: Bearer {token}` header.

#### 6. Get Current User Profile
**GET** `/api/me`

Get authenticated user's profile information.

**Headers:**
```
Authorization: Bearer {your-jwt-token}
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

Logout and invalidate the current JWT token.

**Headers:**
```
Authorization: Bearer {your-jwt-token}
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Successfully logged out"
}
```

---

#### 8. Create New Product
**POST** `/api/produk`

Create a new product (Admin only).

**Headers:**
```
Authorization: Bearer {your-jwt-token}
```

**Request Body:**
```json
{
  "nama": "New Product",
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
    "nama": "New Product",
    "harga": "500000.00",
    "sku": "NP-001",
    "stok": 100,
    "kategori_id": 1
  }
}
```

---

#### 9. Update Product
**PUT** `/api/produk/{id}`

Update an existing product.

**Headers:**
```
Authorization: Bearer {your-jwt-token}
```

**Request Body:**
```json
{
  "nama": "Updated Product Name",
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
    "nama": "Updated Product Name",
    "harga": "550000.00",
    "sku": "NP-001",
    "stok": 120,
    "kategori_id": 1
  }
}
```

---

#### 10. Delete Product
**DELETE** `/api/produk/{id}`

Delete a product.

**Headers:**
```
Authorization: Bearer {your-jwt-token}
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Produk Berhasil Dihapus"
}
```

---

#### 11. Get User's Order History
**GET** `/api/pesanan`

Get all orders for the authenticated user.

**Headers:**
```
Authorization: Bearer {your-jwt-token}
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

#### 12. Get Single Order Details
**GET** `/api/pesanan/{id}`

Get detailed information about a specific order.

**Headers:**
```
Authorization: Bearer {your-jwt-token}
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
      "alamat_tujuan": "Jl. Example No. 123",
      "status_pengiriman": "terkirim"
    }
  }
}
```

---

#### 13. Create New Order
**POST** `/api/pesanan`

Create a new order using the stored procedure `sp_buat_pesanan_enterprise`.

**Headers:**
```
Authorization: Bearer {your-jwt-token}
```

**Request Body:**
```json
{
  "product_id": 1,
  "qty": 2,
  "courier": "JNE",
  "address": "Jl. Example No. 123, Jakarta"
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

#### 14. Get Shipping Monitoring
**GET** `/api/monitoring-pengiriman`

Get shipping monitoring data using the database view `v_monitoring_pengiriman`.

**Headers:**
```
Authorization: Bearer {your-jwt-token}
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

#### 15. Get Audit Logs
**GET** `/api/audit-logs`

Get audit logs for stock changes (Admin only - consider adding admin middleware).

**Headers:**
```
Authorization: Bearer {your-jwt-token}
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

## 🔧 Testing the API

### Using cURL

#### 1. Register
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

#### 3. Get Products (Public)
```bash
curl http://127.0.0.1:8000/api/produk
```

#### 4. Get User Profile (Protected)
```bash
curl http://127.0.0.1:8000/api/me \
  -H "Authorization: Bearer YOUR_JWT_TOKEN_HERE"
```

#### 5. Create Order (Protected)
```bash
curl -X POST http://127.0.0.1:8000/api/pesanan \
  -H "Authorization: Bearer YOUR_JWT_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "product_id": 1,
    "qty": 1,
    "courier": "JNE",
    "address": "Jl. Example No. 123"
  }'
```

---

## 📝 Error Responses

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

## 🚀 Quick Start

1. **Start the Laravel server:**
   ```bash
   php artisan serve
   ```

2. **Test the health endpoint:**
   ```bash
   curl http://127.0.0.1:8000/api/health
   ```

3. **Login with a test account:**
   ```bash
   curl -X POST http://127.0.0.1:8000/api/login \
     -H "Content-Type: application/json" \
     -d '{"email": "user1@mail.com", "kata_sandi": "password123"}'
   ```

4. **Use the returned token for authenticated requests!**

---

## 📚 Additional Notes

- All timestamps are in ISO 8601 format
- All monetary values are in Indonesian Rupiah (IDR)
- The API uses the stored procedure `sp_buat_pesanan_enterprise` for order creation
- Stock changes are automatically logged via database triggers
- The `v_monitoring_pengiriman` view provides real-time shipping monitoring
