# Dokumentasi Arsitektur Database-Web - Basidut E-Commerce

## 📚 Daftar Isi
1. [Arsitektur Sistem](#arsitektur-sistem)
2. [Alur Kerja Database-Web](#alur-kerja-database-web)
3. [Modul dan Komunikasi](#modul-dan-komunikasi)
4. [Testing Guide](#testing-guide)
5. [ERD dan Diagram](#erd-dan-diagram)

---

## 🏗️ Arsitektur Sistem

### Overview

```
┌─────────────────────────────────────────────────────────────┐
│                     BASIDUT E-COMMERCE                      │
│                   Monolithic Architecture                    │
└─────────────────────────────────────────────────────────────┘

┌──────────────┐      ┌──────────────┐      ┌──────────────┐
│   Browser    │◄────►│  Laravel App │◄────►│ MySQL DB     │
│  (Client)    │ HTTP │  (Server)    │ SQL  │  (basidut)   │
└──────────────┘      └──────────────┘      └──────────────┘
```

### Technology Stack

| Layer | Technology | Version |
|-------|-----------|---------|
| **Frontend** | Blade Templates + Bootstrap 5 | - |
| **Backend** | Laravel Framework | 11.x |
| **Database** | MySQL | 8.0+ |
| **Web Server** | PHP Built-in Server | PHP 8.2+ |
| **Font** | Google Fonts (Inter) | - |

---

## 🔄 Alur Kerja Database-Web

### 1. User Registration Flow

```
┌─────────┐    POST /register    ┌──────────┐    INSERT    ┌──────────┐
│ Browser │──────────────────────►│ Laravel  │─────────────►│ Database │
│         │                       │ Auth     │              │ pengguna │
│         │◄──────────────────────│Controller│◄─────────────│          │
└─────────┘   Redirect to /shop  └──────────┘   User ID    └──────────┘
```

**Detail Steps:**
1. User mengisi form registrasi (username, email, password, nama_lengkap)
2. Laravel validates input
3. Password di-hash menggunakan bcrypt
4. Data disimpan ke tabel `pengguna` dengan field `kata_sandi`
5. Auto-login dan redirect ke `/shop`

**SQL Query:**
```sql
INSERT INTO pengguna (username, email, kata_sandi, nama_lengkap, aktif)
VALUES (?, ?, ?, ?, 1);
```

---

### 2. User Login Flow

```
┌─────────┐   POST /login   ┌──────────┐   SELECT   ┌──────────┐
│ Browser │────────────────►│ Laravel  │───────────►│ Database │
│         │                 │ Auth     │            │ pengguna │
│         │◄────────────────│Controller│◄───────────│          │
└─────────┘  Session Cookie └──────────┘  User Data └──────────┘
```

**Detail Steps:**
1. User input email & password
2. Laravel mencari user berdasarkan email
3. Verify password hash dengan `kata_sandi`
4. Create session
5. Redirect ke `/shop`

**SQL Query:**
```sql
SELECT * FROM pengguna 
WHERE email = ? AND aktif = 1;
```

---

### 3. Product Listing Flow

```
┌─────────┐   GET /shop   ┌──────────┐   SELECT   ┌──────────┐
│ Browser │──────────────►│ Produk   │───────────►│ Database │
│         │               │Controller│            │ produk   │
│         │◄──────────────│          │◄───────────│          │
└─────────┘  HTML + Data  └──────────┘  Products  └──────────┘
```

**Detail Steps:**
1. Browser request `/shop`
2. `ProdukController@index` query products dengan stok > 0
3. Data dikirim ke Blade template
4. Render HTML dengan Bootstrap cards
5. Return ke browser

**SQL Query:**
```sql
SELECT * FROM produk 
WHERE stok > 0 
ORDER BY nama ASC;
```

---

### 4. Order Creation Flow (CRITICAL - Stored Procedure)

```
┌─────────┐  POST /api/orders  ┌──────────┐  CALL SP  ┌──────────┐
│ Browser │───────────────────►│ Pesanan  │──────────►│ Database │
│         │  JSON: {           │Controller│           │          │
│         │   product_id,      │          │           │ ┌──────┐ │
│         │   qty,             │          │           │ │ SP   │ │
│         │   courier,         │          │           │ │Logic │ │
│         │   address          │          │           │ └──┬───┘ │
│         │  }                 │          │           │    │     │
│         │                    │          │           │    ▼     │
│         │                    │          │           │ ┌──────┐ │
│         │                    │          │           │ │BEGIN │ │
│         │                    │          │           │ │TRANS │ │
│         │                    │          │           │ └──┬───┘ │
│         │                    │          │           │    │     │
│         │                    │          │           │    ▼     │
│         │                    │          │           │ Lock Row │
│         │                    │          │           │ Validate │
│         │                    │          │           │ Insert   │
│         │                    │          │           │ Update   │
│         │                    │          │           │ Trigger  │
│         │                    │          │           │ COMMIT   │
│         │◄───────────────────│          │◄──────────│          │
└─────────┘  JSON Response     └──────────┘  OUT Vars └──────────┘
```

**Detail Steps:**

1. **Frontend (JavaScript):**
   - User klik "Beli Sekarang"
   - Modal muncul dengan form
   - User isi qty, courier, address
   - AJAX POST ke `/api/orders`

2. **Backend (Laravel):**
   ```php
   // PesananController@store
   DB::statement('CALL sp_buat_pesanan_enterprise(?, ?, ?, ?, ?, @out_id, @out_status)', [
       Auth::id(),      // user_id
       $request->product_id,
       $request->qty,
       $request->courier,
       $request->address
   ]);
   
   $result = DB::select('SELECT @out_id as order_id, @out_status as status');
   ```

3. **Database (Stored Procedure):**
   ```sql
   BEGIN
       -- 1. Lock row produk
       SELECT stok INTO v_stok FROM produk 
       WHERE id = p_product_id FOR UPDATE;
       
       -- 2. Validasi stok
       IF v_stok < p_qty THEN
           SET p_status = 'ERROR: Stok tidak cukup';
           ROLLBACK;
       END IF;
       
       -- 3. Insert pesanan
       INSERT INTO pesanan (...) VALUES (...);
       SET p_order_id = LAST_INSERT_ID();
       
       -- 4. Insert item_pesanan
       INSERT INTO item_pesanan (...) VALUES (...);
       
       -- 5. Insert pengiriman
       INSERT INTO pengiriman (...) VALUES (...);
       
       -- 6. Update stok
       UPDATE produk SET stok = stok - p_qty WHERE id = p_product_id;
       
       -- 7. Trigger akan auto-insert ke log_audit
       
       COMMIT;
       SET p_status = 'SUKSES';
   END
   ```

4. **Response:**
   - Success: `{success: true, order_id: 123, message: "..."}`
   - Error: `{success: false, message: "..."}`

5. **Frontend Callback:**
   - Show toast notification
   - Redirect ke `/orders` setelah 2 detik

---

### 5. Order History Flow

```
┌─────────┐  GET /orders  ┌──────────┐    JOIN    ┌──────────┐
│ Browser │──────────────►│ Pesanan  │───────────►│ Database │
│         │               │Controller│            │ Multiple │
│         │               │          │            │ Tables   │
│         │◄──────────────│          │◄───────────│          │
└─────────┘  HTML Table   └──────────┘  Order Data└──────────┘
```

**Detail Steps:**
1. User klik menu "Pesanan"
2. `PesananController@index` query dengan JOIN
3. Data dari 4 tabel: pesanan, pengiriman, item_pesanan, produk
4. Render ke Blade template
5. Display dalam Bootstrap table

**SQL Query:**
```sql
SELECT 
    p.id, p.nomor_pesanan, p.total, p.status, p.tanggal_pesanan,
    pg.kurir, pg.nomor_resi,
    pr.nama as nama_produk,
    ip.jumlah
FROM pesanan p
LEFT JOIN pengiriman pg ON p.id = pg.pesanan_id
LEFT JOIN item_pesanan ip ON p.id = ip.pesanan_id
LEFT JOIN produk pr ON ip.produk_id = pr.id
WHERE p.pelanggan_id = ?
ORDER BY p.id DESC;
```

---

## 🔗 Modul dan Komunikasi

### Module Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                      BASIDUT MODULES                        │
└─────────────────────────────────────────────────────────────┘

┌──────────────┐   ┌──────────────┐   ┌──────────────┐
│   USER       │   │   PRODUCT    │   │    ORDER     │
│   MODULE     │   │   MODULE     │   │   MODULE     │
│              │   │              │   │              │
│ - pengguna   │   │ - produk     │   │ - pesanan    │
│              │   │ - kategori   │   │ - item_      │
│              │   │              │   │   pesanan    │
└──────┬───────┘   └──────┬───────┘   └──────┬───────┘
       │                  │                  │
       │                  │                  │
       └──────────────────┼──────────────────┘
                          │
                ┌─────────▼─────────┐
                │   LOGISTICS       │
                │   MODULE          │
                │                   │
                │ - pengiriman      │
                └─────────┬─────────┘
                          │
                ┌─────────▼─────────┐
                │   AUDIT           │
                │   MODULE          │
                │                   │
                │ - log_audit       │
                └───────────────────┘
```

### Inter-Module Communication

#### 1. User → Order
```
pengguna.id (PK) ──► pesanan.pelanggan_id (FK)
```
**Komunikasi:** Foreign key relationship
**Query:** `SELECT * FROM pesanan WHERE pelanggan_id = ?`

#### 2. Product → Order
```
produk.id (PK) ──► item_pesanan.produk_id (FK)
```
**Komunikasi:** Many-to-Many via junction table
**Query:** 
```sql
SELECT p.*, ip.jumlah, ip.harga_satuan
FROM produk p
JOIN item_pesanan ip ON p.id = ip.produk_id
WHERE ip.pesanan_id = ?
```

#### 3. Order → Logistics
```
pesanan.id (PK) ──► pengiriman.pesanan_id (FK)
```
**Komunikasi:** One-to-One relationship
**Query:** `SELECT * FROM pengiriman WHERE pesanan_id = ?`

#### 4. All Modules → Audit
```
Trigger on UPDATE/INSERT/DELETE ──► log_audit
```
**Komunikasi:** Database trigger (automatic)
**Example:**
```sql
CREATE TRIGGER trg_audit_stok_update
AFTER UPDATE ON produk
FOR EACH ROW
BEGIN
    IF OLD.stok <> NEW.stok THEN
        INSERT INTO log_audit (...) VALUES (...);
    END IF;
END;
```

---

## 🧪 Testing Guide

### Database Testing

#### 1. Test Stored Procedure
```sql
-- Test order creation
CALL sp_buat_pesanan_enterprise(1, 1, 2, 'JNE', 'Jakarta', @id, @status);
SELECT @id as order_id, @status as status;

-- Verify results
SELECT * FROM pesanan WHERE id = @id;
SELECT * FROM item_pesanan WHERE pesanan_id = @id;
SELECT * FROM pengiriman WHERE pesanan_id = @id;
SELECT stok FROM produk WHERE id = 1; -- Should decrease by 2
SELECT * FROM log_audit ORDER BY id DESC LIMIT 1; -- Should have new entry
```

#### 2. Test Functions
```sql
-- Test calculate total
SELECT fn_hitung_total_pesanan(1);

-- Test stock check
SELECT fn_cek_stok_tersedia(1, 5);

-- Test discount calculation
SELECT fn_hitung_diskon(1, 1000000);
```

#### 3. Test Triggers
```sql
-- Update stock and check audit log
UPDATE produk SET stok = stok - 1 WHERE id = 1;
SELECT * FROM log_audit ORDER BY id DESC LIMIT 1;
```

#### 4. Test Views
```sql
-- Query monitoring view
SELECT * FROM v_monitoring_pengiriman WHERE pelanggan_id = 1;
```

### Web Application Testing

#### 1. Manual Testing Checklist

**Authentication:**
- [ ] Register new user
- [ ] Login with valid credentials
- [ ] Login with invalid credentials
- [ ] Logout

**Product Listing:**
- [ ] View all products
- [ ] Verify stock display
- [ ] Verify price format (Rupiah)

**Order Creation:**
- [ ] Click "Beli Sekarang"
- [ ] Fill order form
- [ ] Submit order
- [ ] Verify toast notification
- [ ] Verify redirect to orders page

**Order History:**
- [ ] View order list
- [ ] Click order detail
- [ ] Verify all information displayed

#### 2. Database Verification After Order

```sql
-- 1. Check new order
SELECT * FROM pesanan ORDER BY id DESC LIMIT 1;

-- 2. Check order items
SELECT * FROM item_pesanan ORDER BY id DESC LIMIT 1;

-- 3. Check shipping
SELECT * FROM pengiriman ORDER BY id DESC LIMIT 1;

-- 4. Verify stock reduction
SELECT nama, stok FROM produk WHERE id = ?;

-- 5. Check audit log
SELECT * FROM log_audit ORDER BY id DESC LIMIT 5;
```

#### 3. API Testing (Postman/cURL)

```bash
# Create Order
curl -X POST http://127.0.0.1:8000/api/orders \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: your-token" \
  -d '{
    "product_id": 1,
    "qty": 2,
    "courier": "JNE",
    "address": "Jakarta Selatan"
  }'
```

Expected Response:
```json
{
  "success": true,
  "message": "Pesanan berhasil dibuat!",
  "order_id": 123
}
```

---

## 📊 ERD dan Diagram

### Entity Relationship Diagram

**Cara Membuat ERD:**

1. **Menggunakan dbdiagram.io:**
   - Buka https://dbdiagram.io
   - Copy paste schema dari `REQUIREMENTS.md`
   - Export sebagai PNG/SVG
   - Simpan di folder `docs/`

2. **Menggunakan Draw.io:**
   - Buka https://app.diagrams.net
   - Pilih template "Entity Relationship"
   - Gambar sesuai schema
   - Export sebagai PNG

3. **Menggunakan MySQL Workbench:**
   - Database → Reverse Engineer
   - Pilih database `basidut`
   - Generate ERD otomatis
   - Export diagram

### ERD Text Representation

```
┌─────────────┐         ┌─────────────┐
│  pengguna   │         │   kategori  │
├─────────────┤         ├─────────────┤
│ PK id       │         │ PK id       │
│    username │         │    nama     │
│    email    │         └─────────────┘
│ kata_sandi  │                │
│nama_lengkap │                │
│    aktif    │                │
└──────┬──────┘                │
       │                       │
       │ 1                     │ 1
       │                       │
       │ N                     │ N
       │                       │
┌──────▼──────┐         ┌──────▼──────┐
│   pesanan   │    N    │   produk    │
├─────────────┤◄────────┤─────────────┤
│ PK id       │         │ PK id       │
│nomor_pesanan│         │    nama     │
│pelanggan_id │         │    harga    │
│    total    │         │    sku      │
│   status    │         │    stok     │
│tanggal_     │         │kategori_id  │
│  pesanan    │         └──────┬──────┘
└──────┬──────┘                │
       │                       │
       │ 1                     │
       │                       │
       │ N                     │ N
       │                       │
┌──────▼──────┐         ┌──────▼──────┐
│pengiriman   │         │item_pesanan │
├─────────────┤         ├─────────────┤
│PK pesanan_id│         │PK pesanan_id│
│    kurir    │         │PK produk_id │
│nomor_resi   │         │    jumlah   │
│status_      │         │harga_satuan │
│ pengiriman  │         └─────────────┘
└─────────────┘

        ┌─────────────┐
        │  log_audit  │
        ├─────────────┤
        │ PK id       │
        │    tabel    │
        │    aksi     │
        │ data_lama   │
        │ data_baru   │
        │  timestamp  │
        └─────────────┘
```

### Sequence Diagram - Order Creation

```
User    Browser    Laravel    Database    Trigger
 │         │          │           │           │
 │  Click  │          │           │           │
 │  Buy    │          │           │           │
 ├────────►│          │           │           │
 │         │  POST    │           │           │
 │         │ /orders  │           │           │
 │         ├─────────►│           │           │
 │         │          │   CALL    │           │
 │         │          │    SP     │           │
 │         │          ├──────────►│           │
 │         │          │           │  BEGIN    │
 │         │          │           ├───────────┤
 │         │          │           │  LOCK     │
 │         │          │           ├───────────┤
 │         │          │           │  INSERT   │
 │         │          │           ├───────────┤
 │         │          │           │  UPDATE   │
 │         │          │           ├──────────►│
 │         │          │           │           │ Audit
 │         │          │           │◄──────────┤
 │         │          │           │  COMMIT   │
 │         │          │           ├───────────┤
 │         │          │  OUT Vars │           │
 │         │          │◄──────────┤           │
 │         │   JSON   │           │           │
 │         │◄─────────┤           │           │
 │  Toast  │          │           │           │
 │◄────────┤          │           │           │
 │         │          │           │           │
```

---

## 📝 Dokumentasi untuk Laporan

### File-file yang Perlu Disiapkan

1. **ERD Diagram** (`docs/ERD.png`)
   - High-level ERD
   - Per-module ERD (opsional)

2. **Sequence Diagram** (`docs/sequence_order_creation.png`)
   - Order creation flow
   - Authentication flow

3. **Architecture Diagram** (`docs/architecture.png`)
   - System overview
   - Module communication

4. **Database Schema** (`database/schema.sql`)
   - Complete CREATE TABLE statements
   - Stored procedures
   - Functions
   - Triggers
   - Views

5. **Optimization Report** (`database/LAPORAN_OPTIMASI.md`)
   - EXPLAIN ANALYZE results
   - Performance benchmarks
   - Recommendations

6. **Testing Report** (`docs/TESTING_REPORT.md`)
   - Test cases
   - Test results
   - Screenshots

### Template Laporan Tugas Besar

```markdown
# LAPORAN TUGAS BESAR BASIS DATA LANJUT
## BASIDUT E-COMMERCE SYSTEM

### BAB 1: PENDAHULUAN
1.1 Latar Belakang
1.2 Tujuan
1.3 Ruang Lingkup

### BAB 2: ANALISIS KEBUTUHAN
2.1 Kebutuhan Fungsional
2.2 Kebutuhan Non-Fungsional
2.3 Identifikasi Modul

### BAB 3: PERANCANGAN DATABASE
3.1 ERD High-Level
3.2 ERD Per Modul
3.3 Normalisasi
3.4 Physical Schema

### BAB 4: IMPLEMENTASI
4.1 Stored Procedure
4.2 Function
4.3 Trigger
4.4 View
4.5 Transaction

### BAB 5: INTEGRASI WEB
5.1 Arsitektur Aplikasi
5.2 API Endpoints
5.3 Autentikasi
5.4 CRUD Operations

### BAB 6: OPTIMASI
6.1 Indexing Strategy
6.2 Query Optimization
6.3 Performance Analysis

### BAB 7: TESTING
7.1 Database Testing
7.2 Web Application Testing
7.3 Integration Testing

### BAB 8: KESIMPULAN
8.1 Kesimpulan
8.2 Saran

### LAMPIRAN
A. Source Code
B. Screenshots
C. Database Dump
```

---

## ✅ Checklist Kelengkapan

### Database
- [x] CREATE TABLE statements
- [x] PRIMARY & FOREIGN KEYs
- [x] UNIQUE constraints
- [x] CHECK constraints
- [x] Stored Procedure (sp_buat_pesanan_enterprise)
- [ ] Functions (4 functions - perlu di-execute)
- [x] Trigger (audit log)
- [x] View (v_monitoring_pengiriman)
- [x] Transaction (dalam SP)

### Web Application
- [x] CRUD 3 modul (User, Product, Order)
- [x] REST API endpoint
- [x] Authentication & Authorization
- [x] JOIN queries
- [x] Subqueries (dalam SP)

### Documentation
- [x] README.md (GitHub-ready)
- [x] REQUIREMENTS.md (technical specs)
- [x] LAPORAN_OPTIMASI.md
- [x] Database functions SQL
- [ ] ERD Diagram (perlu dibuat visual)
- [ ] Sequence Diagram (perlu dibuat visual)
- [ ] Testing Report (perlu dibuat)

### Testing
- [x] Manual testing guide
- [x] SQL test queries
- [x] API test examples
- [ ] Automated tests (opsional)

---

**Prepared by:** Basidut Development Team  
**Date:** 2025-12-19  
**Version:** 1.0
