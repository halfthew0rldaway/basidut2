<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration creates advanced database features:
     * 1. Stored Procedure: sp_buat_pesanan_enterprise
     * 2. Trigger: trg_audit_stok_update
     * 3. Function: hitung_total_pesanan
     * 4. View: v_monitoring_pengiriman
     */
    public function up(): void
    {
        // ====================================================================
        // 1. STORED PROCEDURE: sp_buat_pesanan_enterprise
        // ====================================================================
        // Creates order with ACID transaction, row locking, and automatic stock reduction
        DB::unprepared("
            DROP PROCEDURE IF EXISTS sp_buat_pesanan_enterprise;
            
            CREATE PROCEDURE sp_buat_pesanan_enterprise(
                IN p_pelanggan_id INT,
                IN p_produk_id INT,
                IN p_jumlah INT,
                IN p_kurir VARCHAR(50),
                IN p_alamat TEXT,
                OUT p_pesanan_id INT,
                OUT p_status_msg VARCHAR(100)
            )
            BEGIN
                DECLARE v_harga DECIMAL(10,2);
                DECLARE v_stok INT;
                
                -- Error Handler untuk Rollback otomatis jika ada error SQL
                DECLARE EXIT HANDLER FOR SQLEXCEPTION
                BEGIN
                    ROLLBACK;
                    SET p_status_msg = 'ERROR: Transaksi Dibatalkan (System Error)';
                END;

                -- Transaction BEGIN
                START TRANSACTION;

                -- Locking row produk untuk mencegah race condition (Concurrency Control)
                SELECT harga, stok INTO v_harga, v_stok 
                FROM produk WHERE id = p_produk_id FOR UPDATE;
                
                IF v_stok < p_jumlah THEN
                    ROLLBACK;
                    SET p_status_msg = 'GAGAL: Stok Tidak Mencukupi';
                ELSE
                    -- 1. Kurangi Stok
                    UPDATE produk SET stok = stok - p_jumlah WHERE id = p_produk_id;
                    
                    -- 2. Buat Header Pesanan
                    INSERT INTO pesanan (nomor_pesanan, pelanggan_id, total)
                    VALUES (CONCAT('ORD-', UNIX_TIMESTAMP()), p_pelanggan_id, (v_harga * p_jumlah));
                    SET p_pesanan_id = LAST_INSERT_ID();
                    
                    -- 3. Buat Detail Item
                    INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan)
                    VALUES (p_pesanan_id, p_produk_id, p_jumlah, v_harga);
                    
                    -- 4. Integrasi ke Modul Logistik (Data Pengiriman Awal)
                    INSERT INTO pengiriman (pesanan_id, kurir, alamat_tujuan)
                    VALUES (p_pesanan_id, p_kurir, p_alamat);

                    -- Transaction COMMIT
                    COMMIT;
                    SET p_status_msg = 'SUKSES: Pesanan Berhasil Dibuat';
                END IF;
            END
        ");

        // ====================================================================
        // 2. TRIGGER: trg_audit_stok_update
        // ====================================================================
        // Automatically logs stock changes to log_audit table
        DB::unprepared("
            DROP TRIGGER IF EXISTS trg_audit_stok_update;
            
            CREATE TRIGGER trg_audit_stok_update
            AFTER UPDATE ON produk
            FOR EACH ROW
            BEGIN
                -- Hanya catat jika stok berubah
                IF OLD.stok <> NEW.stok THEN
                    INSERT INTO log_audit (nama_tabel, id_record, aksi, keterangan)
                    VALUES ('produk', OLD.id, 'UPDATE', CONCAT('Stok berubah dari ', OLD.stok, ' menjadi ', NEW.stok));
                END IF;
            END
        ");

        // ====================================================================
        // 3. FUNCTION: hitung_total_pesanan
        // ====================================================================
        // Calculates total amount for an order
        DB::unprepared("
            DROP FUNCTION IF EXISTS hitung_total_pesanan;
            
            CREATE FUNCTION hitung_total_pesanan(p_pesanan_id INT)
            RETURNS DECIMAL(10,2)
            READS SQL DATA
            BEGIN
                DECLARE v_total DECIMAL(10,2);
                SELECT COALESCE(SUM(jumlah * harga_satuan), 0) INTO v_total
                FROM item_pesanan
                WHERE pesanan_id = p_pesanan_id;
                RETURN v_total;
            END
        ");

        // ====================================================================
        // 4. VIEW: v_monitoring_pengiriman
        // ====================================================================
        // Provides real-time shipping monitoring data
        DB::unprepared("
            DROP VIEW IF EXISTS v_monitoring_pengiriman;
            
            CREATE VIEW v_monitoring_pengiriman AS
            SELECT 
                p.id AS pesanan_id,
                p.nomor_pesanan,
                p.pelanggan_id,
                p.total,
                p.status AS status_pesanan,
                pg.kurir,
                pg.nomor_resi,
                pg.status_pengiriman,
                pr.nama AS nama_produk,
                ip.jumlah
            FROM pesanan p
            JOIN pengiriman pg ON p.id = pg.pesanan_id
            JOIN item_pesanan ip ON p.id = ip.pesanan_id
            JOIN produk pr ON ip.produk_id = pr.id
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP VIEW IF EXISTS v_monitoring_pengiriman");
        DB::unprepared("DROP FUNCTION IF EXISTS hitung_total_pesanan");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_audit_stok_update");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_buat_pesanan_enterprise");
    }
};
