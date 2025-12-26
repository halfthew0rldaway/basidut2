# Referensi Cepat API Basidut

## 🔗 Base URL
```
http://127.0.0.1:8000/api
```

## 🔐 Autentikasi

### Login
```bash
POST /api/login
Content-Type: application/json

{
  "email": "user1@mail.com",
  "kata_sandi": "password123"
}
```

### Gunakan Token
```bash
Authorization: Bearer {token-jwt-anda}
```

## 📦 Produk

### Daftar Semua Produk
```bash
GET /api/produk
```

### Detail Produk
```bash
GET /api/produk/{id}
```

### Buat Produk (Terproteksi)
```bash
POST /api/produk
Authorization: Bearer {token}
Content-Type: application/json

{
  "nama": "Produk Baru",
  "harga": 500000,
  "sku": "PRD-001",
  "stok": 100,
  "kategori_id": 1
}
```

## 🛒 Pesanan

### Daftar Pesanan Pengguna (Terproteksi)
```bash
GET /api/pesanan
Authorization: Bearer {token}
```

### Buat Pesanan (Terproteksi)
```bash
POST /api/pesanan
Authorization: Bearer {token}
Content-Type: application/json

{
  "product_id": 1,
  "qty": 2,
  "courier": "JNE",
  "address": "Jl. Contoh No. 123"
}
```

## 🚚 Monitoring (Terproteksi)

### Monitoring Pengiriman
```bash
GET /api/monitoring-pengiriman
Authorization: Bearer {token}
```

### Log Audit
```bash
GET /api/audit-logs
Authorization: Bearer {token}
```

## 🧪 Pengujian Cepat

### 1. Login
```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user1@mail.com","kata_sandi":"password123"}'
```

### 2. Dapatkan Produk
```bash
curl http://127.0.0.1:8000/api/produk
```

### 3. Buat Pesanan
```bash
curl -X POST http://127.0.0.1:8000/api/pesanan \
  -H "Authorization: Bearer TOKEN_ANDA" \
  -H "Content-Type: application/json" \
  -d '{"product_id":1,"qty":1,"courier":"JNE","address":"Alamat Uji"}'
```

## 📚 Dokumentasi Lengkap

Lihat `docs/API_DOCUMENTATION.md` untuk referensi lengkap.
