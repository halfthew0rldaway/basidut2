-- ============================================
-- BASIDUT E-COMMERCE - DATABASE FUNCTIONS
-- File: database_functions.sql
-- ============================================

USE basidut;

-- ============================================
-- FUNCTION 1: Hitung Total Pesanan
-- ============================================
DELIMITER $$

DROP FUNCTION IF EXISTS fn_hitung_total_pesanan$$

CREATE FUNCTION fn_hitung_total_pesanan(p_pesanan_id INT)
RETURNS DECIMAL(15,2)
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_total DECIMAL(15,2);
    
    SELECT COALESCE(SUM(jumlah * harga_satuan), 0) INTO v_total
    FROM item_pesanan
    WHERE pesanan_id = p_pesanan_id;
    
    RETURN v_total;
END$$

DELIMITER ;

-- ============================================
-- FUNCTION 2: Cek Stok Tersedia
-- ============================================
DELIMITER $$

DROP FUNCTION IF EXISTS fn_cek_stok_tersedia$$

CREATE FUNCTION fn_cek_stok_tersedia(p_produk_id INT, p_jumlah INT)
RETURNS BOOLEAN
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_stok INT;
    
    SELECT stok INTO v_stok
    FROM produk
    WHERE id = p_produk_id;
    
    IF v_stok >= p_jumlah THEN
        RETURN TRUE;
    ELSE
        RETURN FALSE;
    END IF;
END$$

DELIMITER ;

-- ============================================
-- FUNCTION 3: Hitung Diskon Member
-- ============================================
DELIMITER $$

DROP FUNCTION IF EXISTS fn_hitung_diskon$$

CREATE FUNCTION fn_hitung_diskon(p_user_id INT, p_total DECIMAL(15,2))
RETURNS DECIMAL(15,2)
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE v_jumlah_pesanan INT;
    DECLARE v_diskon DECIMAL(5,2);
    
    -- Hitung jumlah pesanan user
    SELECT COUNT(*) INTO v_jumlah_pesanan
    FROM pesanan
    WHERE pelanggan_id = p_user_id
    AND status = 'selesai';
    
    -- Tentukan diskon berdasarkan loyalitas
    IF v_jumlah_pesanan >= 10 THEN
        SET v_diskon = 0.15; -- 15% untuk pelanggan setia
    ELSEIF v_jumlah_pesanan >= 5 THEN
        SET v_diskon = 0.10; -- 10% untuk pelanggan regular
    ELSE
        SET v_diskon = 0.00; -- Tidak ada diskon
    END IF;
    
    RETURN p_total * v_diskon;
END$$

DELIMITER ;

-- ============================================
-- FUNCTION 4: Format Nomor Pesanan
-- ============================================
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

-- ============================================
-- TESTING FUNCTIONS
-- ============================================

-- Test 1: Hitung total pesanan
SELECT fn_hitung_total_pesanan(1) AS total_pesanan;

-- Test 2: Cek stok tersedia
SELECT fn_cek_stok_tersedia(1, 5) AS stok_cukup;

-- Test 3: Hitung diskon
SELECT fn_hitung_diskon(1, 1000000) AS diskon;

-- Test 4: Generate nomor pesanan
SELECT fn_generate_nomor_pesanan() AS nomor_pesanan_baru;

-- ============================================
-- CONTOH PENGGUNAAN DALAM QUERY
-- ============================================

-- Menggunakan function dalam SELECT
SELECT 
    p.id,
    p.nomor_pesanan,
    fn_hitung_total_pesanan(p.id) AS total_calculated,
    p.total AS total_stored
FROM pesanan p
LIMIT 5;

-- Menggunakan function dalam WHERE
SELECT * FROM produk
WHERE fn_cek_stok_tersedia(id, 10) = TRUE;
