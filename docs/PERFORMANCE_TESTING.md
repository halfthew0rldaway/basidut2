# Panduan Pengujian Performa & Optimasi

## 🚀 Seeding Dataset Besar

### Langkah 1: Seed 1000+ Baris untuk Pengujian Performa

```bash
# Seed data besar (1000 produk + 500 pesanan)
php artisan db:seed --class=PerformanceTestSeeder
```

**Data yang akan dibuat:**
- ✅ 1000 produk tambahan (total: 1003 produk)
- ✅ 500 pesanan
- ✅ 1500+ item pesanan
- ✅ 500 catatan pengiriman

**Total baris: 3000+ record**

### Langkah 2: Verifikasi Jumlah Data

```bash
php artisan tinker
```

```php
// Periksa jumlah
DB::table('produk')->count();      // Seharusnya 1003
DB::table('pesanan')->count();     // Seharusnya 500+
DB::table('item_pesanan')->count(); // Seharusnya 1500+
DB::table('pengiriman')->count();  // Seharusnya 500+
```

## 📊 Pengujian Optimasi Query

### 1. Uji Performa Query - Sebelum Optimasi

```sql
-- Query tanpa index (lambat)
EXPLAIN SELECT * FROM pesanan 
WHERE pelanggan_id = 1 
ORDER BY tanggal_pesanan DESC;

-- Periksa waktu eksekusi
SET profiling = 1;
SELECT * FROM pesanan WHERE pelanggan_id = 1;
SHOW PROFILES;
```

### 2. Tambahkan Index untuk Optimasi

```sql
-- Tambahkan index pada kolom yang sering di-query
CREATE INDEX idx_pesanan_tanggal ON pesanan(tanggal_pesanan);
CREATE INDEX idx_item_pesanan_produk ON item_pesanan(produk_id);
CREATE INDEX idx_pengiriman_status ON pengiriman(status_pengiriman);
```

### 3. Uji Performa Query - Setelah Optimasi

```sql
-- Query dengan index (cepat)
EXPLAIN SELECT * FROM pesanan 
WHERE pelanggan_id = 1 
ORDER BY tanggal_pesanan DESC;

-- Bandingkan waktu eksekusi
SELECT * FROM pesanan WHERE pelanggan_id = 1;
SHOW PROFILES;
```

### 4. Pengujian Query JOIN Kompleks

```sql
-- Uji JOIN kompleks dengan dataset besar
EXPLAIN SELECT 
    p.nomor_pesanan,
    pg.nama_lengkap,
    pr.nama as produk,
    ip.jumlah,
    pe.status_pengiriman
FROM pesanan p
JOIN pengguna pg ON p.pelanggan_id = pg.id
JOIN item_pesanan ip ON p.id = ip.pesanan_id
JOIN produk pr ON ip.produk_id = pr.id
JOIN pengiriman pe ON p.id = pe.pesanan_id
WHERE p.status = 'selesai'
LIMIT 100;
```

## 🔍 Analisis Performa

### Menggunakan EXPLAIN ANALYZE

```sql
-- Analisis rencana eksekusi query
EXPLAIN ANALYZE
SELECT p.*, COUNT(ip.id) as total_items
FROM pesanan p
LEFT JOIN item_pesanan ip ON p.id = ip.pesanan_id
GROUP BY p.id
HAVING total_items > 1;
```

**Metrik yang perlu diperiksa:**
- **rows**: Jumlah baris yang dipindai
- **filtered**: Persentase baris yang difilter
- **type**: Tipe join (ALL = buruk, ref/eq_ref = baik)
- **key**: Index yang digunakan (NULL = tidak ada index)

### Slow Query Log

Aktifkan logging query lambat:
```sql
SET GLOBAL slow_query_log = 'ON';
SET GLOBAL long_query_time = 1; -- Query lebih lambat dari 1 detik
SET GLOBAL slow_query_log_file = 'C:/mysql/slow-query.log';
```

## 📈 Benchmark Performa

### Hasil Benchmark (Contoh)

| Jenis Query | Sebelum Index | Setelah Index | Peningkatan |
|-------------|---------------|---------------|-------------|
| SELECT Sederhana | 0.15s | 0.02s | 87% lebih cepat |
| Query JOIN | 0.45s | 0.08s | 82% lebih cepat |
| Query Agregat | 0.30s | 0.05s | 83% lebih cepat |
| Query View | 0.25s | 0.06s | 76% lebih cepat |

### Performa Endpoint API

Uji dengan Postman:
```
GET /api/pesanan (500 pesanan)
- Tanpa index: ~450ms
- Dengan index: ~80ms
- Peningkatan: 82%
```

## 🎯 Strategi Optimasi yang Diimplementasikan

### 1. Level Database

✅ **Index** pada:
- `pengguna.email` (query login)
- `pesanan.pelanggan_id` (pesanan pengguna)
- `produk.kategori_id` (filtering kategori)
- `pengiriman.nomor_resi` (pelacakan)

✅ **Foreign Keys** untuk:
- Optimasi query
- Integritas data

✅ **Views** untuk:
- Query JOIN kompleks
- Data yang sering diakses

### 2. Level Aplikasi

✅ **Eager Loading**:
```php
// Buruk (masalah N+1)
$orders = Pesanan::all();
foreach ($orders as $order) {
    echo $order->pengiriman->kurir; // N query
}

// Baik (1 query)
$orders = Pesanan::with('pengiriman')->get();
```

✅ **Query Caching**:
```php
// Cache query yang mahal
Cache::remember('products', 3600, function () {
    return Produk::all();
});
```

### 3. Optimasi Stored Procedure

✅ **Row Locking** untuk konkurensi:
```sql
SELECT ... FOR UPDATE; -- Mencegah race condition
```

✅ **Transaction Batching**:
```sql
START TRANSACTION;
-- Beberapa operasi
COMMIT;
```

## 📝 Daftar Periksa Pengujian

### Sebelum Pengujian Performa
- [ ] Backup database
- [ ] Catat jumlah data saat ini
- [ ] Rekam waktu query baseline

### Selama Pengujian Performa
- [ ] Seed 1000+ baris
- [ ] Jalankan EXPLAIN pada query kritis
- [ ] Aktifkan slow query log
- [ ] Uji endpoint API dengan dataset besar

### Setelah Pengujian Performa
- [ ] Bandingkan waktu eksekusi query
- [ ] Dokumentasikan peningkatan
- [ ] Tambahkan index yang diperlukan
- [ ] Perbarui dokumentasi

## 🔧 Perintah untuk Demonstrasi TB

### 1. Seed Dataset Besar
```bash
php artisan db:seed --class=PerformanceTestSeeder
```

### 2. Tampilkan Jumlah Data
```sql
SELECT 
    (SELECT COUNT(*) FROM produk) as total_produk,
    (SELECT COUNT(*) FROM pesanan) as total_pesanan,
    (SELECT COUNT(*) FROM item_pesanan) as total_item,
    (SELECT COUNT(*) FROM pengiriman) as total_pengiriman;
```

### 3. Tampilkan Performa Query
```sql
-- Sebelum optimasi
EXPLAIN SELECT * FROM pesanan WHERE pelanggan_id = 1;

-- Setelah menambahkan index
CREATE INDEX idx_test ON pesanan(pelanggan_id);
EXPLAIN SELECT * FROM pesanan WHERE pelanggan_id = 1;
```

### 4. Tampilkan Penggunaan Index
```sql
SHOW INDEX FROM pesanan;
SHOW INDEX FROM produk;
```

## 📊 Hasil yang Diharapkan untuk TB

Setelah menjalankan PerformanceTestSeeder:
- ✅ 1003 produk
- ✅ 500+ pesanan
- ✅ 1500+ item pesanan
- ✅ Peningkatan performa terukur dengan index
- ✅ Optimasi query terdemonstrasi
- ✅ Strategi backup terdokumentasi

Ini memenuhi requirement: **"Data banyakin mas 1000 row minimal supaya cek optimasi/performa query"** ✅
