# Panduan Import Postman Collection

## 📥 Cara Mengimpor Collection

### Langkah 1: Buka Postman
Jalankan aplikasi Postman di komputer Anda.

### Langkah 2: Impor Collection
1. Klik tombol **Import** (pojok kiri atas)
2. Klik **Upload Files**
3. Pilih file `Basidut_API_Collection.postman_collection.json`
4. Klik **Import**

### Langkah 3: Pengaturan Environment (Opsional namun Disarankan)
1. Klik **Environments** (sidebar kiri)
2. Klik **+** untuk membuat environment baru
3. Beri nama: `Basidut Local`
4. Tambahkan variabel:
   - `base_url` = `http://127.0.0.1:8000/api`
   - `jwt_token` = (kosongkan, akan terisi otomatis)
5. Klik **Save**
6. Pilih `Basidut Local` dari dropdown environment

## 🧪 Alur Pengujian

### Urutan Pengujian Lengkap

**1. Jalankan Server Laravel**
```bash
php artisan serve
```

**2. Jalankan Pengujian Sesuai Urutan:**

#### A. Alur Autentikasi
1. **1.2 Login - Get JWT Token** ✅
   - Menyimpan token secara otomatis
   - Periksa Console untuk melihat token tersimpan
   
2. **1.3 Get Current User Profile** ✅
   - Memverifikasi token berfungsi
   - Menggunakan token tersimpan secara otomatis

#### B. CRUD Produk (Menguji Constraints)
3. **2.1 Get All Products** ✅
   - Seharusnya mengembalikan 3 produk
   
4. **2.3 Create Product** ✅
   - Menguji INSERT dengan constraints
   - Menguji FOREIGN KEY (kategori_id)
   - Menguji UNIQUE constraint (sku)
   
5. **2.4 Update Product** ✅
   - Ubah ID sesuai produk yang dibuat
   - Menguji operasi UPDATE
   
6. **2.2 Get Single Product** ✅
   - Memverifikasi detail produk

#### C. Pesanan (Menguji Stored Procedure & Transaction)
7. **3.1 Create Order - Test Stored Procedure** ✅
   - **PENTING**: Menguji stored procedure
   - Menguji transaksi ACID
   - Menguji row locking
   - Memicu audit log
   
8. **3.2 Create Order - Test Stock Validation** ✅
   - Seharusnya gagal dengan pesan "Stok Tidak Mencukupi"
   - Menguji ROLLBACK transaksi
   
9. **3.3 Get User's Orders** ✅
   - Menguji query JOIN
   - Seharusnya menampilkan pesanan yang dibuat

#### D. Fitur Advanced
10. **4.1 Shipping Monitoring (View)** ✅
    - Menguji database VIEW
    - Menampilkan data hasil join
    
11. **4.2 Audit Logs (Trigger)** ✅
    - Menguji database TRIGGER
    - Seharusnya menampilkan perubahan stok dari pembuatan pesanan

## 📊 Validasi Setiap Pengujian

### Fitur Database yang Diuji

| Pengujian | Fitur | Requirement TB |
|-----------|-------|----------------|
| 3.1 Create Order | Stored Procedure | ✅ sp_buat_pesanan_enterprise |
| 3.1 Create Order | Transaction | ✅ BEGIN/COMMIT/ROLLBACK |
| 4.2 Audit Logs | Trigger | ✅ trg_audit_stok_update |
| 4.1 Shipping Monitoring | View | ✅ v_monitoring_pengiriman |
| 3.3 Get Orders | Query JOIN | ✅ Multi-table JOIN |
| 2.3 Create Product | Constraints | ✅ CHECK, FK, UNIQUE |
| 1.2 Login | Bcrypt Password | ✅ Hash::make() |

### Fitur API yang Diuji

| Pengujian | Metode HTTP | Fitur |
|-----------|-------------|-------|
| 1.1 Register | POST | Membuat pengguna |
| 1.2 Login | POST | Autentikasi JWT |
| 1.3 Get Profile | GET | Endpoint terproteksi |
| 2.1 Get Products | GET | Operasi baca |
| 2.3 Create Product | POST | Membuat dengan validasi |
| 2.4 Update Product | PUT | Operasi update |
| 2.5 Delete Product | DELETE | Operasi hapus |
| 3.1 Create Order | POST | Pemanggilan stored procedure |

## ✅ Kriteria Keberhasilan

Setelah menjalankan semua pengujian, Anda seharusnya memiliki:

1. **Autentikasi**
   - ✅ Token JWT tersimpan otomatis
   - ✅ Dapat mengakses endpoint terproteksi

2. **Produk**
   - ✅ Produk baru berhasil dibuat
   - ✅ Produk berhasil diupdate
   - ✅ Constraints tervalidasi

3. **Pesanan**
   - ✅ Pesanan dibuat via stored procedure
   - ✅ Stok berkurang
   - ✅ Audit log tercatat

4. **Fitur Advanced**
   - ✅ View mengembalikan data pengiriman
   - ✅ Trigger mencatat perubahan stok

## 🔍 Verifikasi di Database

Setelah menjalankan pengujian, verifikasi di MySQL:

```sql
-- Periksa pesanan yang dibuat
SELECT * FROM pesanan ORDER BY id DESC LIMIT 1;

-- Periksa stok berkurang
SELECT id, nama, stok FROM produk WHERE id = 1;

-- Periksa audit log (trigger)
SELECT * FROM log_audit ORDER BY id DESC LIMIT 5;

-- Periksa view berfungsi
SELECT * FROM v_monitoring_pengiriman;

-- Uji function
SELECT hitung_total_pesanan(1) as total;
```

## 📝 Catatan Penting

- **Penyimpanan Token Otomatis**: Request login secara otomatis menyimpan token JWT ke environment
- **Urutan**: Jalankan pengujian secara berurutan untuk hasil terbaik
- **ID**: Perbarui ID produk/pesanan di URL sesuai kebutuhan
- **Stok**: Setiap pesanan mengurangi stok, mempengaruhi pengujian selanjutnya

## 🐛 Pemecahan Masalah

### Error "Unauthenticated"
- Jalankan **1.2 Login** lagi untuk menyegarkan token
- Periksa token tersimpan di variabel environment

### Error "Stok Tidak Mencukupi"
- Ini adalah hasil yang diharapkan untuk pengujian 3.2
- Untuk pengujian 3.1, kurangi qty atau gunakan produk berbeda

### Error "Table doesn't exist"
- Jalankan `php artisan migrate:fresh --seed`
- Restart server Laravel

## 📚 Dokumentasi Terkait

Untuk dokumentasi API lengkap, lihat:
- `docs/API_DOCUMENTATION.md` - Referensi API lengkap
- `docs/API_TESTING_GUIDE.md` - Skenario pengujian detail
- `docs/MIGRATION_GUIDE.md` - Panduan setup database
