# Panduan Membuat ERD untuk Basidut E-Commerce

## 🎯 Tujuan
Membuat Entity Relationship Diagram (ERD) untuk dokumentasi Tugas Besar

---

## 📊 Opsi 1: Menggunakan dbdiagram.io (RECOMMENDED)

### Langkah-langkah:

1. **Buka Website**
   - Kunjungi: https://dbdiagram.io
   - Klik "Go to App"

2. **Copy Schema Berikut:**

```dbml
// Basidut E-Commerce Database Schema

Table pengguna {
  id int [pk, increment]
  username varchar(50) [unique, not null]
  email varchar(100) [unique, not null]
  kata_sandi varchar(255) [not null]
  nama_lengkap varchar(100) [not null]
  aktif tinyint [default: 1]
  dibuat_pada timestamp [default: `CURRENT_TIMESTAMP`]
  
  indexes {
    email
    username
  }
}

Table kategori {
  id int [pk, increment]
  nama varchar(100) [not null]
  deskripsi text
}

Table produk {
  id int [pk, increment]
  nama varchar(200) [not null]
  harga decimal(15,2) [not null, note: 'CHECK >= 0']
  sku varchar(50) [unique, not null]
  stok int [not null, note: 'CHECK >= 0']
  kategori_id int [ref: > kategori.id]
  dibuat_pada timestamp [default: `CURRENT_TIMESTAMP`]
  
  indexes {
    sku
    kategori_id
    stok
  }
}

Table pesanan {
  id int [pk, increment]
  nomor_pesanan varchar(50) [unique, not null]
  pelanggan_id int [ref: > pengguna.id, not null]
  total decimal(15,2) [not null]
  status enum [note: 'menunggu, dibayar, dikemas, dikirim, selesai, dibatalkan']
  tanggal_pesanan timestamp [default: `CURRENT_TIMESTAMP`]
  
  indexes {
    pelanggan_id
    nomor_pesanan
    tanggal_pesanan
  }
}

Table item_pesanan {
  pesanan_id int [pk, ref: > pesanan.id]
  produk_id int [pk, ref: > produk.id]
  jumlah int [not null]
  harga_satuan decimal(15,2) [not null]
  
  indexes {
    (pesanan_id, produk_id) [pk]
  }
}

Table pengiriman {
  pesanan_id int [pk, ref: - pesanan.id]
  kurir varchar(50) [not null]
  nomor_resi varchar(100)
  alamat_pengiriman text [not null]
  status_pengiriman enum [note: 'diproses, dikirim, diterima']
  tanggal_kirim timestamp
  
  indexes {
    nomor_resi
  }
}

Table log_audit {
  id int [pk, increment]
  tabel varchar(50) [not null]
  aksi varchar(20) [not null]
  data_lama text
  data_baru text
  user_id int
  timestamp timestamp [default: `CURRENT_TIMESTAMP`]
  
  indexes {
    tabel
    timestamp
  }
}

// Relationships
Ref: pesanan.pelanggan_id > pengguna.id
Ref: produk.kategori_id > kategori.id
Ref: item_pesanan.pesanan_id > pesanan.id
Ref: item_pesanan.produk_id > produk.id
Ref: pengiriman.pesanan_id - pesanan.id
```

3. **Paste ke Editor**
   - Paste code di atas ke editor dbdiagram.io
   - Diagram akan otomatis ter-generate

4. **Customize Tampilan**
   - Atur layout dengan drag & drop
   - Pilih theme yang sesuai
   - Tambahkan notes jika perlu

5. **Export Diagram**
   - Klik "Export" di menu atas
   - Pilih format: PNG (untuk laporan) atau SVG (untuk editing)
   - Download dan simpan di folder `docs/ERD.png`

---

## 📊 Opsi 2: Menggunakan MySQL Workbench

### Langkah-langkah:

1. **Buka MySQL Workbench**
   - Connect ke database `basidut`

2. **Reverse Engineer**
   - Menu: Database → Reverse Engineer
   - Pilih connection ke database basidut
   - Next → Next
   - Select schema: basidut
   - Next → Execute

3. **Generate ERD**
   - Workbench akan otomatis membuat ERD
   - Atur layout sesuai keinginan

4. **Export**
   - File → Export → Export as PNG
   - Simpan sebagai `docs/ERD_workbench.png`

---

## 📊 Opsi 3: Menggunakan Draw.io

### Langkah-langkah:

1. **Buka Draw.io**
   - Kunjungi: https://app.diagrams.net
   - Pilih "Create New Diagram"

2. **Pilih Template**
   - Pilih "Entity Relation" dari template
   - Atau mulai dari blank

3. **Gambar Manual**
   - Tambahkan shapes untuk setiap tabel
   - Tambahkan fields di dalam tabel
   - Hubungkan dengan connector untuk relationships

4. **Tabel yang Perlu Digambar:**
   - pengguna (7 fields)
   - kategori (3 fields)
   - produk (6 fields)
   - pesanan (6 fields)
   - item_pesanan (4 fields)
   - pengiriman (6 fields)
   - log_audit (7 fields)

5. **Relationships:**
   - pengguna → pesanan (1:N)
   - kategori → produk (1:N)
   - pesanan → item_pesanan (1:N)
   - produk → item_pesanan (1:N)
   - pesanan → pengiriman (1:1)

6. **Export**
   - File → Export as → PNG
   - Simpan sebagai `docs/ERD_manual.png`

---

## 📋 Checklist ERD

### Elemen yang Harus Ada:

- [ ] **7 Entitas:**
  - [ ] pengguna
  - [ ] kategori
  - [ ] produk
  - [ ] pesanan
  - [ ] item_pesanan
  - [ ] pengiriman
  - [ ] log_audit

- [ ] **Primary Keys** (ditandai dengan PK atau underline)
- [ ] **Foreign Keys** (ditandai dengan FK)
- [ ] **Relationships:**
  - [ ] 1:1 (pesanan - pengiriman)
  - [ ] 1:N (pengguna - pesanan, kategori - produk)
  - [ ] N:N (pesanan - produk via item_pesanan)

- [ ] **Cardinality** (1, N, 0..1, 1..N)
- [ ] **Field Types** (int, varchar, decimal, etc.)
- [ ] **Constraints** (NOT NULL, UNIQUE, CHECK)

---

## 🎨 Tips Desain ERD

### 1. Layout
- Tempatkan tabel utama di tengah (pesanan)
- Tabel referensi di sekitar (pengguna, produk)
- Junction table (item_pesanan) di antara tabel yang dihubungkan

### 2. Warna
- Gunakan warna berbeda untuk setiap modul:
  - 🔵 Biru: User Module (pengguna)
  - 🟢 Hijau: Product Module (produk, kategori)
  - 🟡 Kuning: Order Module (pesanan, item_pesanan)
  - 🟠 Orange: Logistics Module (pengiriman)
  - 🔴 Merah: Audit Module (log_audit)

### 3. Annotations
- Tambahkan notes untuk business rules
- Tandai constraints (CHECK, UNIQUE)
- Tambahkan keterangan untuk ENUM values

---

## 📸 Contoh ERD yang Baik

### High-Level ERD
```
┌─────────────┐
│  PENGGUNA   │
│             │
│ • id (PK)   │
│ • username  │
│ • email     │
└──────┬──────┘
       │ 1
       │
       │ N
┌──────▼──────┐       ┌─────────────┐
│   PESANAN   │   N   │   PRODUK    │
│             │◄──────┤             │
│ • id (PK)   │       │ • id (PK)   │
│ • pelanggan │       │ • nama      │
│   _id (FK)  │       │ • harga     │
│ • total     │       │ • stok      │
└──────┬──────┘       └──────┬──────┘
       │ 1                   │
       │                     │
       │ 1                   │ N
┌──────▼──────┐       ┌──────▼──────┐
│ PENGIRIMAN  │       │ITEM_PESANAN │
│             │       │             │
│ • pesanan_id│       │ • pesanan_id│
│   (PK,FK)   │       │   (PK,FK)   │
│ • kurir     │       │ • produk_id │
└─────────────┘       │   (PK,FK)   │
                      └─────────────┘
```

---

## 📝 Dokumentasi ERD untuk Laporan

### Yang Perlu Dijelaskan:

1. **Deskripsi Setiap Entitas**
   ```
   Tabel pengguna:
   - Menyimpan data user yang dapat login ke sistem
   - Primary Key: id (auto increment)
   - Unique: email, username
   - Field kata_sandi untuk password (hashed)
   ```

2. **Penjelasan Relationships**
   ```
   pengguna → pesanan (1:N):
   - Satu user dapat memiliki banyak pesanan
   - Setiap pesanan harus memiliki satu user
   - Foreign Key: pesanan.pelanggan_id → pengguna.id
   ```

3. **Business Rules**
   ```
   - Stok produk tidak boleh negatif (CHECK >= 0)
   - Harga produk tidak boleh negatif (CHECK >= 0)
   - Email dan username harus unique
   - Setiap pesanan harus memiliki minimal 1 item
   ```

---

## ✅ Hasil Akhir

Setelah selesai, Anda harus memiliki:

1. **ERD High-Level** (`docs/ERD.png`)
   - Menampilkan semua entitas dan relationships
   - Ukuran: 1920x1080 atau lebih besar
   - Format: PNG atau SVG

2. **ERD Per Modul** (Opsional)
   - `docs/ERD_user_module.png`
   - `docs/ERD_product_module.png`
   - `docs/ERD_order_module.png`

3. **Dokumentasi ERD** (`docs/ERD_EXPLANATION.md`)
   - Penjelasan setiap entitas
   - Penjelasan relationships
   - Business rules

---

## 🔗 Resources

- **dbdiagram.io:** https://dbdiagram.io
- **Draw.io:** https://app.diagrams.net
- **MySQL Workbench:** https://www.mysql.com/products/workbench/
- **ERD Tutorial:** https://www.lucidchart.com/pages/er-diagrams

---

**Estimasi Waktu:** 30-60 menit  
**Tingkat Kesulitan:** ⭐⭐⭐ (Sedang)  
**Tools Recommended:** dbdiagram.io (paling cepat dan mudah)
