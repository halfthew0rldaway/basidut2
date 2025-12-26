# Performance Testing & Optimization Guide

## 🚀 Seeding Large Dataset

### Step 1: Seed 1000+ Rows for Performance Testing

```bash
# Seed data besar (1000 products + 500 orders)
php artisan db:seed --class=PerformanceTestSeeder
```

**Data yang akan dibuat:**
- ✅ 1000 products tambahan (total: 1003 products)
- ✅ 500 orders
- ✅ 1500+ order items
- ✅ 500 shipping records

**Total rows: 3000+ records**

### Step 2: Verify Data Count

```bash
php artisan tinker
```

```php
// Check counts
DB::table('produk')->count();      // Should be 1003
DB::table('pesanan')->count();     // Should be 500+
DB::table('item_pesanan')->count(); // Should be 1500+
DB::table('pengiriman')->count();  // Should be 500+
```

## 📊 Query Optimization Testing

### 1. Test Query Performance - Before Optimization

```sql
-- Query tanpa index (slow)
EXPLAIN SELECT * FROM pesanan 
WHERE pelanggan_id = 1 
ORDER BY tanggal_pesanan DESC;

-- Check execution time
SET profiling = 1;
SELECT * FROM pesanan WHERE pelanggan_id = 1;
SHOW PROFILES;
```

### 2. Add Indexes for Optimization

```sql
-- Add index on frequently queried columns
CREATE INDEX idx_pesanan_tanggal ON pesanan(tanggal_pesanan);
CREATE INDEX idx_item_pesanan_produk ON item_pesanan(produk_id);
CREATE INDEX idx_pengiriman_status ON pengiriman(status_pengiriman);
```

### 3. Test Query Performance - After Optimization

```sql
-- Query dengan index (fast)
EXPLAIN SELECT * FROM pesanan 
WHERE pelanggan_id = 1 
ORDER BY tanggal_pesanan DESC;

-- Compare execution time
SELECT * FROM pesanan WHERE pelanggan_id = 1;
SHOW PROFILES;
```

### 4. Complex JOIN Query Testing

```sql
-- Test complex JOIN with large dataset
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

## 🔍 Performance Analysis

### Using EXPLAIN ANALYZE

```sql
-- Analyze query execution plan
EXPLAIN ANALYZE
SELECT p.*, COUNT(ip.id) as total_items
FROM pesanan p
LEFT JOIN item_pesanan ip ON p.id = ip.pesanan_id
GROUP BY p.id
HAVING total_items > 1;
```

**Metrics to check:**
- **rows**: Number of rows scanned
- **filtered**: Percentage of rows filtered
- **type**: Join type (ALL = bad, ref/eq_ref = good)
- **key**: Index used (NULL = no index)

### Slow Query Log

Enable slow query logging:
```sql
SET GLOBAL slow_query_log = 'ON';
SET GLOBAL long_query_time = 1; -- Queries slower than 1 second
SET GLOBAL slow_query_log_file = 'C:/mysql/slow-query.log';
```

## 📈 Performance Benchmarks

### Benchmark Results (Example)

| Query Type | Before Index | After Index | Improvement |
|------------|--------------|-------------|-------------|
| Simple SELECT | 0.15s | 0.02s | 87% faster |
| JOIN Query | 0.45s | 0.08s | 82% faster |
| Aggregate Query | 0.30s | 0.05s | 83% faster |
| View Query | 0.25s | 0.06s | 76% faster |

### API Endpoint Performance

Test dengan Postman:
```
GET /api/pesanan (500 orders)
- Without index: ~450ms
- With index: ~80ms
- Improvement: 82%
```

## 🎯 Optimization Strategies Implemented

### 1. Database Level

✅ **Indexes** on:
- `pengguna.email` (login queries)
- `pesanan.pelanggan_id` (user orders)
- `produk.kategori_id` (category filtering)
- `pengiriman.nomor_resi` (tracking)

✅ **Foreign Keys** for:
- Query optimization
- Data integrity

✅ **Views** for:
- Complex JOIN queries
- Frequently accessed data

### 2. Application Level

✅ **Eager Loading**:
```php
// Bad (N+1 problem)
$orders = Pesanan::all();
foreach ($orders as $order) {
    echo $order->pengiriman->kurir; // N queries
}

// Good (1 query)
$orders = Pesanan::with('pengiriman')->get();
```

✅ **Query Caching**:
```php
// Cache expensive queries
Cache::remember('products', 3600, function () {
    return Produk::all();
});
```

### 3. Stored Procedure Optimization

✅ **Row Locking** for concurrency:
```sql
SELECT ... FOR UPDATE; -- Prevents race conditions
```

✅ **Transaction Batching**:
```sql
START TRANSACTION;
-- Multiple operations
COMMIT;
```

## 📝 Testing Checklist

### Before Performance Test
- [ ] Backup database
- [ ] Note current data counts
- [ ] Record baseline query times

### During Performance Test
- [ ] Seed 1000+ rows
- [ ] Run EXPLAIN on critical queries
- [ ] Enable slow query log
- [ ] Test API endpoints with large dataset

### After Performance Test
- [ ] Compare query execution times
- [ ] Document improvements
- [ ] Add necessary indexes
- [ ] Update documentation

## 🔧 Commands for TB Demonstration

### 1. Seed Large Dataset
```bash
php artisan db:seed --class=PerformanceTestSeeder
```

### 2. Show Data Counts
```sql
SELECT 
    (SELECT COUNT(*) FROM produk) as total_products,
    (SELECT COUNT(*) FROM pesanan) as total_orders,
    (SELECT COUNT(*) FROM item_pesanan) as total_items,
    (SELECT COUNT(*) FROM pengiriman) as total_shipments;
```

### 3. Show Query Performance
```sql
-- Before optimization
EXPLAIN SELECT * FROM pesanan WHERE pelanggan_id = 1;

-- After adding index
CREATE INDEX idx_test ON pesanan(pelanggan_id);
EXPLAIN SELECT * FROM pesanan WHERE pelanggan_id = 1;
```

### 4. Show Index Usage
```sql
SHOW INDEX FROM pesanan;
SHOW INDEX FROM produk;
```

## 📊 Expected Results for TB

After running PerformanceTestSeeder:
- ✅ 1003 products
- ✅ 500+ orders
- ✅ 1500+ order items
- ✅ Measurable performance improvement with indexes
- ✅ Query optimization demonstrated
- ✅ Backup strategy documented

This meets the requirement: **"Data banyakin mas 1000 row minimal supaya cek optimasi/performa query"** ✅
