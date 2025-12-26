-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.4.3 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.11.0.7065
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for basidut
CREATE DATABASE IF NOT EXISTS `basidut` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `basidut`;

-- Dumping structure for table basidut.cache
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table basidut.cache: ~0 rows (approximately)

-- Dumping structure for table basidut.cache_locks
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table basidut.cache_locks: ~0 rows (approximately)

-- Dumping structure for table basidut.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table basidut.failed_jobs: ~0 rows (approximately)

-- Dumping structure for function basidut.hitung_total_pesanan
DELIMITER //
CREATE FUNCTION `hitung_total_pesanan`(p_pesanan_id INT) RETURNS decimal(10,2)
    READS SQL DATA
BEGIN
    DECLARE v_total DECIMAL(10,2);
    SELECT COALESCE(SUM(jumlah * harga_satuan),0) INTO v_total
    FROM item_pesanan
    WHERE pesanan_id = p_pesanan_id;
    RETURN v_total;
END//
DELIMITER ;

-- Dumping structure for table basidut.item_pesanan
CREATE TABLE IF NOT EXISTS `item_pesanan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pesanan_id` int NOT NULL,
  `produk_id` int NOT NULL,
  `jumlah` int NOT NULL,
  `harga_satuan` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pesanan_id` (`pesanan_id`),
  KEY `produk_id` (`produk_id`),
  CONSTRAINT `item_pesanan_ibfk_1` FOREIGN KEY (`pesanan_id`) REFERENCES `pesanan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `item_pesanan_ibfk_2` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`),
  CONSTRAINT `chk_jumlah_order` CHECK ((`jumlah` > 0))
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table basidut.item_pesanan: ~2 rows (approximately)
INSERT INTO `item_pesanan` (`id`, `pesanan_id`, `produk_id`, `jumlah`, `harga_satuan`) VALUES
	(1, 1, 2, 1, 8000000.00),
	(2, 2, 1, 1, 15000000.00);

-- Dumping structure for table basidut.jobs
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table basidut.jobs: ~0 rows (approximately)

-- Dumping structure for table basidut.job_batches
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table basidut.job_batches: ~0 rows (approximately)

-- Dumping structure for table basidut.kategori
CREATE TABLE IF NOT EXISTS `kategori` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table basidut.kategori: ~3 rows (approximately)
INSERT INTO `kategori` (`id`, `nama`) VALUES
	(1, 'Elektronik'),
	(2, 'Fashion'),
	(3, 'Rumah Tangga');

-- Dumping structure for table basidut.log_audit
CREATE TABLE IF NOT EXISTS `log_audit` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_tabel` varchar(100) NOT NULL,
  `id_record` int DEFAULT NULL,
  `aksi` enum('INSERT','UPDATE','DELETE') NOT NULL,
  `keterangan` text,
  `user_pelaku` varchar(50) DEFAULT 'SYSTEM',
  `waktu` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table basidut.log_audit: ~1 rows (approximately)
INSERT INTO `log_audit` (`id`, `nama_tabel`, `id_record`, `aksi`, `keterangan`, `user_pelaku`, `waktu`) VALUES
	(1, 'produk', 2, 'UPDATE', 'Stok berubah dari 100 menjadi 99', 'SYSTEM', '2025-12-19 10:45:47'),
	(2, 'produk', 1, 'UPDATE', 'Stok berubah dari 50 menjadi 49', 'SYSTEM', '2025-12-19 10:52:30');

-- Dumping structure for table basidut.log_benchmark
CREATE TABLE IF NOT EXISTS `log_benchmark` (
  `id` int NOT NULL AUTO_INCREMENT,
  `skenario` varchar(100) DEFAULT NULL,
  `waktu_eksekusi_ms` double DEFAULT NULL,
  `dibuat_pada` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table basidut.log_benchmark: ~0 rows (approximately)

-- Dumping structure for table basidut.metode_pembayaran
CREATE TABLE IF NOT EXISTS `metode_pembayaran` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nama` (`nama`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table basidut.metode_pembayaran: ~2 rows (approximately)
INSERT INTO `metode_pembayaran` (`id`, `nama`) VALUES
	(1, 'Transfer Bank'),
	(2, 'Kartu Kredit');

-- Dumping structure for table basidut.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table basidut.migrations: ~1 rows (approximately)
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '0001_01_01_000000_create_users_table', 1),
	(2, '0001_01_01_000001_create_cache_table', 1),
	(3, '0001_01_01_000002_create_jobs_table', 1);

-- Dumping structure for table basidut.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table basidut.password_reset_tokens: ~0 rows (approximately)

-- Dumping structure for table basidut.pembayaran
CREATE TABLE IF NOT EXISTS `pembayaran` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pesanan_id` int NOT NULL,
  `metode_pembayaran_id` int DEFAULT NULL,
  `jumlah` decimal(10,2) NOT NULL,
  `status` enum('menunggu','sukses','gagal') NOT NULL DEFAULT 'menunggu',
  `waktu_bayar` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pesanan_id` (`pesanan_id`),
  KEY `metode_pembayaran_id` (`metode_pembayaran_id`),
  CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`pesanan_id`) REFERENCES `pesanan` (`id`),
  CONSTRAINT `pembayaran_ibfk_2` FOREIGN KEY (`metode_pembayaran_id`) REFERENCES `metode_pembayaran` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table basidut.pembayaran: ~0 rows (approximately)

-- Dumping structure for table basidut.pengguna
CREATE TABLE IF NOT EXISTS `pengguna` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `kata_sandi` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `aktif` tinyint(1) DEFAULT '1',
  `dibuat_pada` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_pengguna_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table basidut.pengguna: ~101 rows (approximately)
INSERT INTO `pengguna` (`id`, `username`, `email`, `kata_sandi`, `nama_lengkap`, `aktif`, `dibuat_pada`) VALUES
	(1, 'user1', 'user1@mail.com', 'password123', 'Pengguna 1', 1, '2025-12-19 10:41:01'),
	(2, 'user2', 'user2@mail.com', 'password123', 'Pengguna 2', 1, '2025-12-19 10:41:01'),
	(3, 'user3', 'user3@mail.com', 'password123', 'Pengguna 3', 1, '2025-12-19 10:41:01'),
	(4, 'user4', 'user4@mail.com', 'password123', 'Pengguna 4', 1, '2025-12-19 10:41:01'),
	(5, 'user5', 'user5@mail.com', 'password123', 'Pengguna 5', 1, '2025-12-19 10:41:01'),
	(6, 'user6', 'user6@mail.com', 'password123', 'Pengguna 6', 1, '2025-12-19 10:41:01'),
	(7, 'user7', 'user7@mail.com', 'password123', 'Pengguna 7', 1, '2025-12-19 10:41:01'),
	(8, 'user8', 'user8@mail.com', 'password123', 'Pengguna 8', 1, '2025-12-19 10:41:01'),
	(9, 'user9', 'user9@mail.com', 'password123', 'Pengguna 9', 1, '2025-12-19 10:41:01'),
	(10, 'user10', 'user10@mail.com', 'password123', 'Pengguna 10', 1, '2025-12-19 10:41:01'),
	(11, 'user11', 'user11@mail.com', 'password123', 'Pengguna 11', 1, '2025-12-19 10:41:01'),
	(12, 'user12', 'user12@mail.com', 'password123', 'Pengguna 12', 1, '2025-12-19 10:41:01'),
	(13, 'user13', 'user13@mail.com', 'password123', 'Pengguna 13', 1, '2025-12-19 10:41:01'),
	(14, 'user14', 'user14@mail.com', 'password123', 'Pengguna 14', 1, '2025-12-19 10:41:01'),
	(15, 'user15', 'user15@mail.com', 'password123', 'Pengguna 15', 1, '2025-12-19 10:41:01'),
	(16, 'user16', 'user16@mail.com', 'password123', 'Pengguna 16', 1, '2025-12-19 10:41:01'),
	(17, 'user17', 'user17@mail.com', 'password123', 'Pengguna 17', 1, '2025-12-19 10:41:01'),
	(18, 'user18', 'user18@mail.com', 'password123', 'Pengguna 18', 1, '2025-12-19 10:41:01'),
	(19, 'user19', 'user19@mail.com', 'password123', 'Pengguna 19', 1, '2025-12-19 10:41:01'),
	(20, 'user20', 'user20@mail.com', 'password123', 'Pengguna 20', 1, '2025-12-19 10:41:01'),
	(21, 'user21', 'user21@mail.com', 'password123', 'Pengguna 21', 1, '2025-12-19 10:41:01'),
	(22, 'user22', 'user22@mail.com', 'password123', 'Pengguna 22', 1, '2025-12-19 10:41:01'),
	(23, 'user23', 'user23@mail.com', 'password123', 'Pengguna 23', 1, '2025-12-19 10:41:01'),
	(24, 'user24', 'user24@mail.com', 'password123', 'Pengguna 24', 1, '2025-12-19 10:41:01'),
	(25, 'user25', 'user25@mail.com', 'password123', 'Pengguna 25', 1, '2025-12-19 10:41:01'),
	(26, 'user26', 'user26@mail.com', 'password123', 'Pengguna 26', 1, '2025-12-19 10:41:01'),
	(27, 'user27', 'user27@mail.com', 'password123', 'Pengguna 27', 1, '2025-12-19 10:41:01'),
	(28, 'user28', 'user28@mail.com', 'password123', 'Pengguna 28', 1, '2025-12-19 10:41:01'),
	(29, 'user29', 'user29@mail.com', 'password123', 'Pengguna 29', 1, '2025-12-19 10:41:01'),
	(30, 'user30', 'user30@mail.com', 'password123', 'Pengguna 30', 1, '2025-12-19 10:41:01'),
	(31, 'user31', 'user31@mail.com', 'password123', 'Pengguna 31', 1, '2025-12-19 10:41:01'),
	(32, 'user32', 'user32@mail.com', 'password123', 'Pengguna 32', 1, '2025-12-19 10:41:01'),
	(33, 'user33', 'user33@mail.com', 'password123', 'Pengguna 33', 1, '2025-12-19 10:41:01'),
	(34, 'user34', 'user34@mail.com', 'password123', 'Pengguna 34', 1, '2025-12-19 10:41:01'),
	(35, 'user35', 'user35@mail.com', 'password123', 'Pengguna 35', 1, '2025-12-19 10:41:01'),
	(36, 'user36', 'user36@mail.com', 'password123', 'Pengguna 36', 1, '2025-12-19 10:41:01'),
	(37, 'user37', 'user37@mail.com', 'password123', 'Pengguna 37', 1, '2025-12-19 10:41:01'),
	(38, 'user38', 'user38@mail.com', 'password123', 'Pengguna 38', 1, '2025-12-19 10:41:02'),
	(39, 'user39', 'user39@mail.com', 'password123', 'Pengguna 39', 1, '2025-12-19 10:41:02'),
	(40, 'user40', 'user40@mail.com', 'password123', 'Pengguna 40', 1, '2025-12-19 10:41:02'),
	(41, 'user41', 'user41@mail.com', 'password123', 'Pengguna 41', 1, '2025-12-19 10:41:02'),
	(42, 'user42', 'user42@mail.com', 'password123', 'Pengguna 42', 1, '2025-12-19 10:41:02'),
	(43, 'user43', 'user43@mail.com', 'password123', 'Pengguna 43', 1, '2025-12-19 10:41:02'),
	(44, 'user44', 'user44@mail.com', 'password123', 'Pengguna 44', 1, '2025-12-19 10:41:02'),
	(45, 'user45', 'user45@mail.com', 'password123', 'Pengguna 45', 1, '2025-12-19 10:41:02'),
	(46, 'user46', 'user46@mail.com', 'password123', 'Pengguna 46', 1, '2025-12-19 10:41:02'),
	(47, 'user47', 'user47@mail.com', 'password123', 'Pengguna 47', 1, '2025-12-19 10:41:02'),
	(48, 'user48', 'user48@mail.com', 'password123', 'Pengguna 48', 1, '2025-12-19 10:41:02'),
	(49, 'user49', 'user49@mail.com', 'password123', 'Pengguna 49', 1, '2025-12-19 10:41:02'),
	(50, 'user50', 'user50@mail.com', 'password123', 'Pengguna 50', 1, '2025-12-19 10:41:02'),
	(51, 'user51', 'user51@mail.com', 'password123', 'Pengguna 51', 1, '2025-12-19 10:41:02'),
	(52, 'user52', 'user52@mail.com', 'password123', 'Pengguna 52', 1, '2025-12-19 10:41:02'),
	(53, 'user53', 'user53@mail.com', 'password123', 'Pengguna 53', 1, '2025-12-19 10:41:02'),
	(54, 'user54', 'user54@mail.com', 'password123', 'Pengguna 54', 1, '2025-12-19 10:41:02'),
	(55, 'user55', 'user55@mail.com', 'password123', 'Pengguna 55', 1, '2025-12-19 10:41:02'),
	(56, 'user56', 'user56@mail.com', 'password123', 'Pengguna 56', 1, '2025-12-19 10:41:02'),
	(57, 'user57', 'user57@mail.com', 'password123', 'Pengguna 57', 1, '2025-12-19 10:41:02'),
	(58, 'user58', 'user58@mail.com', 'password123', 'Pengguna 58', 1, '2025-12-19 10:41:02'),
	(59, 'user59', 'user59@mail.com', 'password123', 'Pengguna 59', 1, '2025-12-19 10:41:02'),
	(60, 'user60', 'user60@mail.com', 'password123', 'Pengguna 60', 1, '2025-12-19 10:41:02'),
	(61, 'user61', 'user61@mail.com', 'password123', 'Pengguna 61', 1, '2025-12-19 10:41:02'),
	(62, 'user62', 'user62@mail.com', 'password123', 'Pengguna 62', 1, '2025-12-19 10:41:02'),
	(63, 'user63', 'user63@mail.com', 'password123', 'Pengguna 63', 1, '2025-12-19 10:41:02'),
	(64, 'user64', 'user64@mail.com', 'password123', 'Pengguna 64', 1, '2025-12-19 10:41:02'),
	(65, 'user65', 'user65@mail.com', 'password123', 'Pengguna 65', 1, '2025-12-19 10:41:02'),
	(66, 'user66', 'user66@mail.com', 'password123', 'Pengguna 66', 1, '2025-12-19 10:41:02'),
	(67, 'user67', 'user67@mail.com', 'password123', 'Pengguna 67', 1, '2025-12-19 10:41:02'),
	(68, 'user68', 'user68@mail.com', 'password123', 'Pengguna 68', 1, '2025-12-19 10:41:02'),
	(69, 'user69', 'user69@mail.com', 'password123', 'Pengguna 69', 1, '2025-12-19 10:41:02'),
	(70, 'user70', 'user70@mail.com', 'password123', 'Pengguna 70', 1, '2025-12-19 10:41:02'),
	(71, 'user71', 'user71@mail.com', 'password123', 'Pengguna 71', 1, '2025-12-19 10:41:02'),
	(72, 'user72', 'user72@mail.com', 'password123', 'Pengguna 72', 1, '2025-12-19 10:41:02'),
	(73, 'user73', 'user73@mail.com', 'password123', 'Pengguna 73', 1, '2025-12-19 10:41:02'),
	(74, 'user74', 'user74@mail.com', 'password123', 'Pengguna 74', 1, '2025-12-19 10:41:02'),
	(75, 'user75', 'user75@mail.com', 'password123', 'Pengguna 75', 1, '2025-12-19 10:41:02'),
	(76, 'user76', 'user76@mail.com', 'password123', 'Pengguna 76', 1, '2025-12-19 10:41:02'),
	(77, 'user77', 'user77@mail.com', 'password123', 'Pengguna 77', 1, '2025-12-19 10:41:02'),
	(78, 'user78', 'user78@mail.com', 'password123', 'Pengguna 78', 1, '2025-12-19 10:41:02'),
	(79, 'user79', 'user79@mail.com', 'password123', 'Pengguna 79', 1, '2025-12-19 10:41:02'),
	(80, 'user80', 'user80@mail.com', 'password123', 'Pengguna 80', 1, '2025-12-19 10:41:02'),
	(81, 'user81', 'user81@mail.com', 'password123', 'Pengguna 81', 1, '2025-12-19 10:41:02'),
	(82, 'user82', 'user82@mail.com', 'password123', 'Pengguna 82', 1, '2025-12-19 10:41:02'),
	(83, 'user83', 'user83@mail.com', 'password123', 'Pengguna 83', 1, '2025-12-19 10:41:02'),
	(84, 'user84', 'user84@mail.com', 'password123', 'Pengguna 84', 1, '2025-12-19 10:41:02'),
	(85, 'user85', 'user85@mail.com', 'password123', 'Pengguna 85', 1, '2025-12-19 10:41:02'),
	(86, 'user86', 'user86@mail.com', 'password123', 'Pengguna 86', 1, '2025-12-19 10:41:02'),
	(87, 'user87', 'user87@mail.com', 'password123', 'Pengguna 87', 1, '2025-12-19 10:41:02'),
	(88, 'user88', 'user88@mail.com', 'password123', 'Pengguna 88', 1, '2025-12-19 10:41:02'),
	(89, 'user89', 'user89@mail.com', 'password123', 'Pengguna 89', 1, '2025-12-19 10:41:02'),
	(90, 'user90', 'user90@mail.com', 'password123', 'Pengguna 90', 1, '2025-12-19 10:41:02'),
	(91, 'user91', 'user91@mail.com', 'password123', 'Pengguna 91', 1, '2025-12-19 10:41:02'),
	(92, 'user92', 'user92@mail.com', 'password123', 'Pengguna 92', 1, '2025-12-19 10:41:02'),
	(93, 'user93', 'user93@mail.com', 'password123', 'Pengguna 93', 1, '2025-12-19 10:41:02'),
	(94, 'user94', 'user94@mail.com', 'password123', 'Pengguna 94', 1, '2025-12-19 10:41:02'),
	(95, 'user95', 'user95@mail.com', 'password123', 'Pengguna 95', 1, '2025-12-19 10:41:02'),
	(96, 'user96', 'user96@mail.com', 'password123', 'Pengguna 96', 1, '2025-12-19 10:41:02'),
	(97, 'user97', 'user97@mail.com', 'password123', 'Pengguna 97', 1, '2025-12-19 10:41:02'),
	(98, 'user98', 'user98@mail.com', 'password123', 'Pengguna 98', 1, '2025-12-19 10:41:02'),
	(99, 'user99', 'user99@mail.com', 'password123', 'Pengguna 99', 1, '2025-12-19 10:41:02'),
	(100, 'user100', 'user100@mail.com', 'password123', 'Pengguna 100', 1, '2025-12-19 10:41:02'),
	(101, 'basidut', 'basidut@jokowi.com', '$2y$12$bz3jN1tD/7AAwNFO6k2ln.jxnxuAC9A3qGkAiirQ4/fs2z9wE9/XK', 'basidut', 1, '2025-12-19 10:45:02');

-- Dumping structure for table basidut.pengguna_peran
CREATE TABLE IF NOT EXISTS `pengguna_peran` (
  `pengguna_id` int NOT NULL,
  `peran_id` int NOT NULL,
  PRIMARY KEY (`pengguna_id`,`peran_id`),
  KEY `peran_id` (`peran_id`),
  CONSTRAINT `pengguna_peran_ibfk_1` FOREIGN KEY (`pengguna_id`) REFERENCES `pengguna` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pengguna_peran_ibfk_2` FOREIGN KEY (`peran_id`) REFERENCES `peran` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table basidut.pengguna_peran: ~0 rows (approximately)

-- Dumping structure for table basidut.pengiriman
CREATE TABLE IF NOT EXISTS `pengiriman` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pesanan_id` int NOT NULL,
  `kurir` varchar(50) NOT NULL,
  `nomor_resi` varchar(100) DEFAULT NULL,
  `alamat_tujuan` text NOT NULL,
  `biaya_ongkir` decimal(10,2) DEFAULT '0.00',
  `status_pengiriman` enum('siap_kirim','dalam_perjalanan','terkirim','retur') DEFAULT 'siap_kirim',
  `update_terakhir` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `pesanan_id` (`pesanan_id`),
  KEY `idx_resi` (`nomor_resi`),
  CONSTRAINT `pengiriman_ibfk_1` FOREIGN KEY (`pesanan_id`) REFERENCES `pesanan` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table basidut.pengiriman: ~2 rows (approximately)
INSERT INTO `pengiriman` (`id`, `pesanan_id`, `kurir`, `nomor_resi`, `alamat_tujuan`, `biaya_ongkir`, `status_pengiriman`, `update_terakhir`) VALUES
	(1, 1, 'JNT', NULL, 'rumah', 0.00, 'siap_kirim', '2025-12-19 10:45:47'),
	(2, 2, 'JNT', NULL, 'w', 0.00, 'siap_kirim', '2025-12-19 10:52:30');

-- Dumping structure for table basidut.peran
CREATE TABLE IF NOT EXISTS `peran` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nama` (`nama`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table basidut.peran: ~0 rows (approximately)

-- Dumping structure for table basidut.pesanan
CREATE TABLE IF NOT EXISTS `pesanan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nomor_pesanan` varchar(50) NOT NULL,
  `pelanggan_id` int NOT NULL,
  `tanggal_pesanan` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` enum('menunggu','dibayar','dikemas','dikirim','selesai','dibatalkan') NOT NULL DEFAULT 'menunggu',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nomor_pesanan` (`nomor_pesanan`),
  KEY `idx_pesanan_pelanggan` (`pelanggan_id`),
  CONSTRAINT `pesanan_ibfk_1` FOREIGN KEY (`pelanggan_id`) REFERENCES `pengguna` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table basidut.pesanan: ~1 rows (approximately)
INSERT INTO `pesanan` (`id`, `nomor_pesanan`, `pelanggan_id`, `tanggal_pesanan`, `total`, `status`) VALUES
	(1, 'ORD-1766141147', 101, '2025-12-19 10:45:47', 8000000.00, 'menunggu'),
	(2, 'ORD-1766141550', 101, '2025-12-19 10:52:30', 15000000.00, 'menunggu');

-- Dumping structure for table basidut.produk
CREATE TABLE IF NOT EXISTS `produk` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(200) NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `sku` varchar(50) NOT NULL,
  `kategori_id` int NOT NULL,
  `stok` int NOT NULL DEFAULT '0',
  `aktif` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku` (`sku`),
  KEY `idx_produk_kategori` (`kategori_id`),
  CONSTRAINT `produk_ibfk_1` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`),
  CONSTRAINT `chk_harga_positif` CHECK ((`harga` >= 0)),
  CONSTRAINT `chk_stok_positif` CHECK ((`stok` >= 0))
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table basidut.produk: ~3 rows (approximately)
INSERT INTO `produk` (`id`, `nama`, `harga`, `sku`, `kategori_id`, `stok`, `aktif`) VALUES
	(1, 'Laptop Pro', 15000000.00, 'LPT-001', 1, 49, 1),
	(2, 'Smartphone X', 8000000.00, 'HP-001', 1, 99, 1),
	(3, 'Kemeja Kantor', 150000.00, 'BJU-001', 2, 200, 1);

-- Dumping structure for table basidut.sessions
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table basidut.sessions: ~1 rows (approximately)
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
	('5e5VCNbrCjh8r4tL9NdbWef4HpKwkxPNlpIuQSj5', 101, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiUjVGRXpnclM2dHZDZHVlZWlNZHk3eG54TVBNVHkxSzlOMVRkS0hOMyI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjI2OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvc2hvcCI7czo1OiJyb3V0ZSI7czo0OiJzaG9wIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTAxO30=', 1766151236);

-- Dumping structure for procedure basidut.sp_buat_pesanan_enterprise
DELIMITER //
CREATE PROCEDURE `sp_buat_pesanan_enterprise`(
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

    -- [cite: 155] Transaction BEGIN
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

        -- [cite: 155] Transaction COMMIT
        COMMIT;
        SET p_status_msg = 'SUKSES: Pesanan Berhasil Dibuat';
    END IF;
END//
DELIMITER ;

-- Dumping structure for procedure basidut.sp_seed_dummy_data
DELIMITER //
CREATE PROCEDURE `sp_seed_dummy_data`()
BEGIN
    DECLARE i INT DEFAULT 1;
    
    -- Insert Kategori & Produk
    INSERT INTO kategori (nama) VALUES ('Elektronik'), ('Fashion'), ('Rumah Tangga');
    INSERT INTO produk (nama, harga, sku, kategori_id, stok) VALUES 
    ('Laptop Pro', 15000000, 'LPT-001', 1, 50),
    ('Smartphone X', 8000000, 'HP-001', 1, 100),
    ('Kemeja Kantor', 150000, 'BJU-001', 2, 200);

    INSERT INTO metode_pembayaran (nama) VALUES ('Transfer Bank'), ('Kartu Kredit');

    -- Insert 100 User Dummy
    WHILE i <= 100 DO
        INSERT INTO pengguna (username, email, kata_sandi, nama_lengkap)
        VALUES (CONCAT('user', i), CONCAT('user', i, '@mail.com'), 'password123', CONCAT('Pengguna ', i));
        SET i = i + 1;
    END WHILE;
END//
DELIMITER ;

-- Dumping structure for table basidut.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table basidut.users: ~0 rows (approximately)

-- Dumping structure for trigger basidut.trg_audit_stok_update
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `trg_audit_stok_update` AFTER UPDATE ON `produk` FOR EACH ROW BEGIN
    -- Hanya catat jika stok berubah
    IF OLD.stok <> NEW.stok THEN
        INSERT INTO log_audit (nama_tabel, id_record, aksi, keterangan)
        VALUES ('produk', OLD.id, 'UPDATE', CONCAT('Stok berubah dari ', OLD.stok, ' menjadi ', NEW.stok));
    END IF;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
