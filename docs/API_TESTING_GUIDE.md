# API Testing Guide - Postman Collection

## 🎯 Overview
This guide provides complete API testing scenarios for the Basidut e-commerce system, focusing on database features and API endpoints.

## 📦 Postman Collection

### Base URL
```
http://127.0.0.1:8000/api
```

---

## 1️⃣ Authentication Tests

### 1.1 Register New User
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

**Expected Response (201):**
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

### 1.2 Login (Get JWT Token)
**POST** `/api/login`

**Body (JSON):**
```json
{
    "email": "user1@mail.com",
    "kata_sandi": "password123"
}
```

**Expected Response (200):**
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

**⚠️ IMPORTANT:** Save the `token` value for use in protected endpoints!

---

### 1.3 Get Current User Profile
**GET** `/api/me`

**Headers:**
```
Authorization: Bearer {YOUR_TOKEN_HERE}
```

**Expected Response (200):**
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

## 2️⃣ Product Tests (CRUD)

### 2.1 Get All Products
**GET** `/api/produk`

**Expected Response (200):**
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
        },
        {
            "id": 2,
            "nama": "Smartphone X",
            "harga": "8000000.00",
            "sku": "HP-001",
            "stok": 100,
            "kategori_id": 1
        },
        {
            "id": 3,
            "nama": "Kemeja Kantor",
            "harga": "150000.00",
            "sku": "BJU-001",
            "stok": 200,
            "kategori_id": 2
        }
    ]
}
```

---

### 2.2 Get Single Product
**GET** `/api/produk/1`

**Expected Response (200):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "nama": "Laptop Pro",
        "harga": "15000000.00",
        "sku": "LPT-001",
        "stok": 50,
        "kategori_id": 1
    }
}
```

---

### 2.3 Create Product (Protected)
**POST** `/api/produk`

**Headers:**
```
Authorization: Bearer {YOUR_TOKEN_HERE}
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

**Expected Response (201):**
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

### 2.4 Update Product (Protected)
**PUT** `/api/produk/4`

**Headers:**
```
Authorization: Bearer {YOUR_TOKEN_HERE}
Content-Type: application/json
```

**Body (JSON):**
```json
{
    "nama": "Mouse Gaming RGB",
    "harga": 400000,
    "stok": 80
}
```

**Expected Response (200):**
```json
{
    "success": true,
    "message": "Produk Berhasil Diperbarui",
    "data": {
        "id": 4,
        "nama": "Mouse Gaming RGB",
        "harga": "400000.00",
        "sku": "MSE-001",
        "stok": 80,
        "kategori_id": 1
    }
}
```

---

### 2.5 Delete Product (Protected)
**DELETE** `/api/produk/4`

**Headers:**
```
Authorization: Bearer {YOUR_TOKEN_HERE}
```

**Expected Response (200):**
```json
{
    "success": true,
    "message": "Produk Berhasil Dihapus"
}
```

---

## 3️⃣ Order Tests (Stored Procedure)

### 3.1 Create Order - Tests Stored Procedure
**POST** `/api/pesanan`

**Headers:**
```
Authorization: Bearer {YOUR_TOKEN_HERE}
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

**Expected Response (201):**
```json
{
    "success": true,
    "message": "Pesanan berhasil dibuat!",
    "order_id": 1
}
```

**Database Effects:**
- ✅ Stock reduced (trigger logs this to `log_audit`)
- ✅ Order created in `pesanan` table
- ✅ Order item created in `item_pesanan` table
- ✅ Shipping record created in `pengiriman` table
- ✅ Transaction committed (ACID)

---

### 3.2 Get User's Orders
**GET** `/api/pesanan`

**Headers:**
```
Authorization: Bearer {YOUR_TOKEN_HERE}
```

**Expected Response (200):**
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
            "nomor_resi": null,
            "status_pengiriman": "siap_kirim",
            "nama_produk": "Laptop Pro",
            "jumlah": 2,
            "harga_satuan": "15000000.00"
        }
    ]
}
```

---

### 3.3 Get Single Order Details
**GET** `/api/pesanan/1`

**Headers:**
```
Authorization: Bearer {YOUR_TOKEN_HERE}
```

**Expected Response (200):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "nomor_pesanan": "ORD-1735223456",
        "pelanggan_id": 1,
        "total": "30000000.00",
        "status": "menunggu",
        "item_pesanan": [
            {
                "pesanan_id": 1,
                "produk_id": 1,
                "jumlah": 2,
                "harga_satuan": "15000000.00",
                "produk": {
                    "id": 1,
                    "nama": "Laptop Pro",
                    "harga": "15000000.00",
                    "sku": "LPT-001",
                    "stok": 48
                }
            }
        ],
        "pengiriman": {
            "pesanan_id": 1,
            "kurir": "JNE",
            "nomor_resi": null,
            "alamat_tujuan": "Jl. Sudirman No. 123, Jakarta Pusat",
            "status_pengiriman": "siap_kirim"
        }
    }
}
```

---

## 4️⃣ Advanced Features Tests

### 4.1 Shipping Monitoring (View)
**GET** `/api/monitoring-pengiriman`

**Headers:**
```
Authorization: Bearer {YOUR_TOKEN_HERE}
```

**Expected Response (200):**
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
            "nomor_resi": null,
            "status_pengiriman": "siap_kirim",
            "nama_produk": "Laptop Pro",
            "jumlah": 2
        }
    ]
}
```

**Database Feature:** Uses view `v_monitoring_pengiriman`

---

### 4.2 Audit Logs (Trigger)
**GET** `/api/audit-logs`

**Headers:**
```
Authorization: Bearer {YOUR_TOKEN_HERE}
```

**Expected Response (200):**
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

**Database Feature:** Populated by trigger `trg_audit_stok_update`

---

## 5️⃣ Health Check

### 5.1 API Health
**GET** `/api/health`

**Expected Response (200):**
```json
{
    "status": "ok",
    "timestamp": "2025-12-26T14:30:00.000000Z",
    "service": "Basidut API",
    "version": "1.0.0"
}
```

---

## 🧪 Testing Scenarios

### Scenario 1: Complete Order Flow
1. Login → Get token
2. Get products → Choose product
3. Create order → Stock reduces, audit logged
4. Get orders → Verify order created
5. Check audit logs → Verify stock change logged

### Scenario 2: Stock Validation
1. Create order with qty > stock
2. Expected: Error "Stok Tidak Mencukupi"
3. Verify: Stock unchanged, no order created

### Scenario 3: Transaction Rollback
1. Create order with invalid data
2. Expected: Transaction rolled back
3. Verify: No partial data in database

---

## 📊 Database Queries to Verify

After creating orders, verify in database:

```sql
-- Check stock reduced
SELECT id, nama, stok FROM produk WHERE id = 1;

-- Check order created
SELECT * FROM pesanan ORDER BY id DESC LIMIT 1;

-- Check order items
SELECT * FROM item_pesanan ORDER BY id DESC LIMIT 1;

-- Check shipping created
SELECT * FROM pengiriman ORDER BY id DESC LIMIT 1;

-- Check audit log
SELECT * FROM log_audit ORDER BY id DESC LIMIT 5;

-- Test function
SELECT hitung_total_pesanan(1) as total;

-- Test view
SELECT * FROM v_monitoring_pengiriman;
```

---

## ✅ Success Criteria

All tests should pass with:
- ✅ Proper HTTP status codes
- ✅ Correct JSON response format
- ✅ Data persisted in database
- ✅ Stored procedure executes successfully
- ✅ Trigger logs audit entries
- ✅ Function calculates correctly
- ✅ View returns joined data
- ✅ Transactions maintain ACID properties
