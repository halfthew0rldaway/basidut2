# Script SQL Lengkap untuk Testing Database

## 🎯 Cara Menggunakan

1. Buka HeidiSQL
2. Connect ke database `basidut`
3. Copy-paste dan execute script sesuai kebutuhan

---

## 📦 1. INSTALL DATABASE FUNCTIONS

```sql
USE basidut;

-- Function 1: Hitung Total Pesanan
DELIMITER $$
DROP FUNCTION IF EXISTS fn_hitung_total_pesanan$$
CREATE FUNCTION fn_hitung_total_pesanan(p_pesanan_id INT)
RETURNS DECIMAL(15,2)
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_total DECIMAL(15,2);
    SELECT COALESCE(SUM(jumlah * harga_satuan), 0) INTO v_total
    FROM item_pesanan WHERE pesanan_id = p_pesanan_id;
    RETURN v_total;
END$$
DELIMITER ;

-- Function 2: Cek Stok Tersedia
DELIMITER $$
DROP FUNCTION IF EXISTS fn_cek_stok_tersedia$$
CREATE FUNCTION fn_cek_stok_tersedia(p_produk_id INT, p_jumlah INT)
RETURNS BOOLEAN
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_stok INT;
    SELECT stok INTO v_stok FROM produk WHERE id = p_produk_id;
    IF v_stok >= p_jumlah THEN
        RETURN TRUE;
    ELSE
        RETURN FALSE;
    END IF;
END$$
DELIMITER ;

-- Function 3: Hitung Diskon
DELIMITER $$
DROP FUNCTION IF EXISTS fn_hitung_diskon$$
CREATE FUNCTION fn_hitung_diskon(p_user_id INT, p_total DECIMAL(15,2))
RETURNS DECIMAL(15,2)
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_jumlah_pesanan INT;
    DECLARE v_diskon DECIMAL(5,2);
    SELECT COUNT(*) INTO v_jumlah_pesanan
    FROM pesanan WHERE pelanggan_id = p_user_id AND status = 'selesai';
    IF v_jumlah_pesanan >= 10 THEN
        SET v_diskon = 0.15;
    ELSEIF v_jumlah_pesanan >= 5 THEN
        SET v_diskon = 0.10;
    ELSE
        SET v_diskon = 0.00;
    END IF;
    RETURN p_total * v_diskon;
END$$
DELIMITER ;

-- Function 4: Generate Nomor Pesanan
DELIMITER $$
DROP FUNCTION IF EXISTS fn_generate_nomor_pesanan$$
CREATE FUNCTION fn_generate_nomor_pesanan()
RETURNS VARCHAR(20)
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_count INT;
    DECLARE v_nomor VARCHAR(20);
    SELECT COUNT(*) INTO v_count FROM pesanan;
    SET v_nomor = CONCAT('ORD-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(v_count + 1, 5, '0'));
    RETURN v_nomor;
END$$
DELIMITER ;
```

---

## 🧪 2. TEST DATABASE FUNCTIONS

```sql
-- Test 1: Hitung total pesanan (jika sudah ada pesanan)
SELECT fn_hitung_total_pesanan(1) AS total_pesanan;

-- Test 2: Cek stok tersedia
SELECT 
    id,
    nama,
    stok,
    fn_cek_stok_tersedia(id, 5) AS stok_cukup_untuk_5
FROM produk;

-- Test 3: Hitung diskon untuk user
SELECT 
    id,
    nama_lengkap,
    fn_hitung_diskon(id, 1000000) AS diskon_dari_1jt
FROM pengguna
LIMIT 5;

-- Test 4: Generate nomor pesanan
SELECT fn_generate_nomor_pesanan() AS nomor_pesanan_baru;
```

---

## 🧪 3. TEST STORED PROCEDURE

```sql
-- Test create order
CALL sp_buat_pesanan_enterprise(
    1,              -- user_id (user1)
    1,              -- product_id (Laptop Pro)
    2,              -- qty
    'JNE',          -- courier
    'Jakarta Selatan, Jl. Sudirman No. 123',  -- address
    @out_id,        -- output: order_id
    @out_status     -- output: status
);

-- Lihat hasil
SELECT @out_id as order_id, @out_status as status;

-- Verify pesanan dibuat
SELECT * FROM pesanan WHERE id = @out_id;
SELECT * FROM item_pesanan WHERE pesanan_id = @out_id;
SELECT * FROM pengiriman WHERE pesanan_id = @out_id;

-- Cek stok berkurang
SELECT id, nama, stok FROM produk WHERE id = 1;

-- Cek audit log
SELECT * FROM log_audit ORDER BY id DESC LIMIT 5;
```

---

## 🧪 4. TEST TRIGGER

```sql
-- Sebelum update, cek stok
SELECT id, nama, stok FROM produk WHERE id = 1;

-- Update stok manual
UPDATE produk SET stok = stok - 1 WHERE id = 1;

-- Cek audit log (trigger harus mencatat)
SELECT * FROM log_audit 
WHERE tabel = 'produk' 
ORDER BY id DESC LIMIT 1;

-- Kembalikan stok
UPDATE produk SET stok = stok + 1 WHERE id = 1;
```

---

## 🧪 5. TEST VIEW

```sql
-- Query monitoring view
SELECT * FROM v_monitoring_pengiriman;

-- Filter by user
SELECT * FROM v_monitoring_pengiriman 
WHERE pelanggan_id = 1;

-- Filter by status
SELECT * FROM v_monitoring_pengiriman 
WHERE status_pesanan = 'menunggu';
```

---

## 📊 6. QUERY UNTUK LAPORAN

### 6.1 Statistik Produk
```sql
-- Produk terlaris
SELECT 
    p.nama,
    SUM(ip.jumlah) as total_terjual,
    SUM(ip.jumlah * ip.harga_satuan) as total_revenue
FROM produk p
JOIN item_pesanan ip ON p.id = ip.produk_id
GROUP BY p.id, p.nama
ORDER BY total_terjual DESC;

-- Produk dengan stok rendah
SELECT id, nama, stok
FROM produk
WHERE stok < 10
ORDER BY stok ASC;
```

### 6.2 Statistik Pesanan
```sql
-- Total pesanan per status
SELECT 
    status,
    COUNT(*) as jumlah_pesanan,
    SUM(total) as total_nilai
FROM pesanan
GROUP BY status;

-- Pesanan per bulan
SELECT 
    DATE_FORMAT(tanggal_pesanan, '%Y-%m') as bulan,
    COUNT(*) as jumlah_pesanan,
    SUM(total) as total_nilai
FROM pesanan
GROUP BY DATE_FORMAT(tanggal_pesanan, '%Y-%m')
ORDER BY bulan DESC;
```

### 6.3 Statistik User
```sql
-- Top customers
SELECT 
    u.id,
    u.nama_lengkap,
    COUNT(p.id) as jumlah_pesanan,
    SUM(p.total) as total_belanja
FROM pengguna u
LEFT JOIN pesanan p ON u.id = p.pelanggan_id
GROUP BY u.id, u.nama_lengkap
ORDER BY total_belanja DESC
LIMIT 10;

-- User yang belum pernah order
SELECT id, nama_lengkap, email
FROM pengguna
WHERE id NOT IN (SELECT DISTINCT pelanggan_id FROM pesanan);
```

---

## 🔍 7. EXPLAIN ANALYZE QUERIES

```sql
-- Query 1: Order history dengan JOIN
EXPLAIN SELECT 
    p.id, p.nomor_pesanan, p.total, p.status,
    pg.kurir, pg.nomor_resi,
    pr.nama as nama_produk,
    ip.jumlah
FROM pesanan p
LEFT JOIN pengiriman pg ON p.id = pg.pesanan_id
LEFT JOIN item_pesanan ip ON p.id = ip.pesanan_id
LEFT JOIN produk pr ON ip.produk_id = pr.id
WHERE p.pelanggan_id = 1
ORDER BY p.id DESC;

-- Query 2: Product listing
EXPLAIN SELECT * FROM produk 
WHERE stok > 0 
ORDER BY nama ASC;

-- Query 3: Search by email
EXPLAIN SELECT * FROM pengguna 
WHERE email = 'user1@mail.com';
```

---

## 🛠️ 8. OPTIMASI INDEXES

```sql
-- Tambah index untuk optimasi
CREATE INDEX idx_pesanan_pelanggan_tanggal 
ON pesanan(pelanggan_id, tanggal_pesanan DESC);

CREATE INDEX idx_produk_stok 
ON produk(stok);

CREATE INDEX idx_produk_nama 
ON produk(nama);

-- Cek indexes yang ada
SHOW INDEX FROM pesanan;
SHOW INDEX FROM produk;
SHOW INDEX FROM pengguna;

-- Analyze table untuk update statistics
ANALYZE TABLE pesanan;
ANALYZE TABLE produk;
ANALYZE TABLE item_pesanan;
```

---

## 📊 9. DATABASE STATISTICS

```sql
-- Table sizes
SELECT 
    TABLE_NAME,
    TABLE_ROWS,
    ROUND(DATA_LENGTH / 1024, 2) AS 'Data (KB)',
    ROUND(INDEX_LENGTH / 1024, 2) AS 'Index (KB)',
    ROUND((DATA_LENGTH + INDEX_LENGTH) / 1024, 2) AS 'Total (KB)'
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = 'basidut'
ORDER BY (DATA_LENGTH + INDEX_LENGTH) DESC;

-- Index cardinality
SELECT 
    TABLE_NAME,
    INDEX_NAME,
    SEQ_IN_INDEX,
    COLUMN_NAME,
    CARDINALITY
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = 'basidut'
ORDER BY TABLE_NAME, INDEX_NAME, SEQ_IN_INDEX;
```

---

## 🧹 10. CLEANUP & RESET (HATI-HATI!)

```sql
-- HANYA UNTUK TESTING! JANGAN JALANKAN DI PRODUCTION!

-- Reset semua data (keep structure)
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE log_audit;
TRUNCATE TABLE pengiriman;
TRUNCATE TABLE item_pesanan;
TRUNCATE TABLE pesanan;
TRUNCATE TABLE produk;
TRUNCATE TABLE kategori;
TRUNCATE TABLE pengguna;
SET FOREIGN_KEY_CHECKS = 1;

-- Seed ulang dummy data
CALL sp_seed_dummy_data();
```

---

## ✅ CHECKLIST TESTING

### Database Functions
- [ ] fn_hitung_total_pesanan - tested
- [ ] fn_cek_stok_tersedia - tested
- [ ] fn_hitung_diskon - tested
- [ ] fn_generate_nomor_pesanan - tested

### Stored Procedure
- [ ] sp_buat_pesanan_enterprise - tested
- [ ] Verify: pesanan created
- [ ] Verify: item_pesanan created
- [ ] Verify: pengiriman created
- [ ] Verify: stok reduced
- [ ] Verify: audit log created

### Trigger
- [ ] trg_audit_stok_update - tested
- [ ] Verify: log_audit entry created

### View
- [ ] v_monitoring_pengiriman - tested
- [ ] Verify: data accurate

### Performance
- [ ] EXPLAIN ANALYZE executed
- [ ] Indexes created
- [ ] Statistics updated

---

**Prepared for:** Tugas Besar Basis Data Lanjut  
**Database:** basidut  
**Date:** 2025-12-19
