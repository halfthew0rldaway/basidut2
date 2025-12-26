# Database Backup & Restore Strategy

## 📦 Backup Strategy menggunakan mysqldump

### 1. Full Database Backup

#### Backup Lengkap (Struktur + Data)
```bash
mysqldump -u root -p basidut > backup/basidut_full_backup_$(date +%Y%m%d_%H%M%S).sql
```

**Hasil**: File SQL berisi semua tabel, data, stored procedures, triggers, functions, dan views.

#### Backup dengan Kompresi (Hemat Space)
```bash
mysqldump -u root -p basidut | gzip > backup/basidut_backup_$(date +%Y%m%d_%H%M%S).sql.gz
```

### 2. Backup Struktur Saja (Tanpa Data)

```bash
mysqldump -u root -p --no-data basidut > backup/basidut_schema_only.sql
```

**Kegunaan**: Untuk deployment ke server baru atau dokumentasi struktur database.

### 3. Backup Data Saja (Tanpa Struktur)

```bash
mysqldump -u root -p --no-create-info basidut > backup/basidut_data_only.sql
```

### 4. Backup Tabel Tertentu

```bash
# Backup tabel pesanan dan item_pesanan saja
mysqldump -u root -p basidut pesanan item_pesanan > backup/basidut_orders_backup.sql
```

### 5. Backup dengan Advanced Features

```bash
# Include stored procedures, triggers, functions, events
mysqldump -u root -p \
  --routines \
  --triggers \
  --events \
  basidut > backup/basidut_complete_backup.sql
```

**Flags:**
- `--routines`: Include stored procedures dan functions
- `--triggers`: Include triggers
- `--events`: Include scheduled events

## 🔄 Restore Strategy

### 1. Restore Full Database

```bash
# Buat database baru (jika belum ada)
mysql -u root -p -e "CREATE DATABASE basidut"

# Restore dari backup
mysql -u root -p basidut < backup/basidut_full_backup_20251226_210000.sql
```

### 2. Restore dari Compressed Backup

```bash
gunzip < backup/basidut_backup_20251226_210000.sql.gz | mysql -u root -p basidut
```

### 3. Restore Tabel Tertentu

```bash
mysql -u root -p basidut < backup/basidut_orders_backup.sql
```

## 📅 Automated Backup Schedule

### Windows Task Scheduler Script

**File**: `backup_basidut.bat`
```batch
@echo off
set TIMESTAMP=%date:~-4,4%%date:~-10,2%%date:~-7,2%_%time:~0,2%%time:~3,2%%time:~6,2%
set TIMESTAMP=%TIMESTAMP: =0%
set BACKUP_DIR=C:\backup\basidut
set MYSQL_USER=root
set MYSQL_PASS=your_password

mkdir %BACKUP_DIR% 2>nul

echo Starting backup at %date% %time%
mysqldump -u %MYSQL_USER% -p%MYSQL_PASS% --routines --triggers basidut > %BACKUP_DIR%\basidut_%TIMESTAMP%.sql

echo Compressing backup...
"C:\Program Files\7-Zip\7z.exe" a -tgzip %BACKUP_DIR%\basidut_%TIMESTAMP%.sql.gz %BACKUP_DIR%\basidut_%TIMESTAMP%.sql

del %BACKUP_DIR%\basidut_%TIMESTAMP%.sql

echo Backup completed: basidut_%TIMESTAMP%.sql.gz
```

### Linux Cron Job

```bash
# Edit crontab
crontab -e

# Backup setiap hari jam 2 pagi
0 2 * * * /usr/bin/mysqldump -u root -pYOUR_PASSWORD --routines --triggers basidut | gzip > /backup/basidut_$(date +\%Y\%m\%d).sql.gz

# Backup setiap 6 jam
0 */6 * * * /usr/bin/mysqldump -u root -pYOUR_PASSWORD basidut | gzip > /backup/basidut_$(date +\%Y\%m\%d_\%H\%M).sql.gz
```

## 🗂️ Backup Retention Policy

### Strategi Penyimpanan

```
backup/
├── daily/          # Backup harian (simpan 7 hari)
├── weekly/         # Backup mingguan (simpan 4 minggu)
├── monthly/        # Backup bulanan (simpan 12 bulan)
└── yearly/         # Backup tahunan (simpan selamanya)
```

### Cleanup Script (Windows)

```batch
@echo off
REM Hapus backup lebih dari 7 hari
forfiles /p "C:\backup\basidut\daily" /s /m *.sql.gz /d -7 /c "cmd /c del @path"

REM Hapus backup mingguan lebih dari 30 hari
forfiles /p "C:\backup\basidut\weekly" /s /m *.sql.gz /d -30 /c "cmd /c del @path"
```

## 🔍 Verifikasi Backup

### 1. Cek Ukuran File
```bash
ls -lh backup/basidut_*.sql.gz
```

### 2. Test Restore ke Database Temporary
```bash
# Buat database test
mysql -u root -p -e "CREATE DATABASE basidut_test"

# Restore ke database test
mysql -u root -p basidut_test < backup/basidut_full_backup.sql

# Verifikasi data
mysql -u root -p basidut_test -e "SELECT COUNT(*) FROM pesanan"

# Hapus database test
mysql -u root -p -e "DROP DATABASE basidut_test"
```

### 3. Cek Integritas Backup
```bash
# Cek apakah file corrupt
gzip -t backup/basidut_backup.sql.gz

# Jika OK, tidak ada output
# Jika corrupt, akan muncul error
```

## 📊 Backup untuk Performance Testing

### Backup Sebelum Load Testing
```bash
# Backup sebelum seed 1000+ rows
mysqldump -u root -p basidut > backup/before_performance_test.sql

# Seed data besar
php artisan db:seed --class=PerformanceTestSeeder

# Backup setelah seed
mysqldump -u root -p basidut > backup/after_performance_test.sql
```

### Restore ke Kondisi Awal
```bash
# Kembali ke kondisi sebelum testing
mysql -u root -p basidut < backup/before_performance_test.sql
```

## 🎯 Best Practices

### 1. Backup Sebelum Perubahan Besar
```bash
# Sebelum migration
mysqldump -u root -p basidut > backup/before_migration_$(date +%Y%m%d).sql

# Sebelum update production
mysqldump -u root -p basidut > backup/before_deployment_$(date +%Y%m%d).sql
```

### 2. Enkripsi Backup (Untuk Data Sensitif)
```bash
# Backup dengan enkripsi
mysqldump -u root -p basidut | gzip | openssl enc -aes-256-cbc -salt -out backup/basidut_encrypted.sql.gz.enc

# Restore dari encrypted backup
openssl enc -d -aes-256-cbc -in backup/basidut_encrypted.sql.gz.enc | gunzip | mysql -u root -p basidut
```

### 3. Remote Backup
```bash
# Backup ke server remote via SSH
mysqldump -u root -p basidut | ssh user@remote-server "cat > /backup/basidut_$(date +%Y%m%d).sql"
```

## 📝 Dokumentasi untuk TB

### Strategi Backup yang Diimplementasikan:

1. **Full Backup Harian** - Backup lengkap setiap hari
2. **Incremental Backup** - Backup perubahan setiap 6 jam
3. **Retention Policy** - Simpan 7 hari backup harian, 4 minggu backup mingguan
4. **Automated Schedule** - Menggunakan Task Scheduler/Cron
5. **Verification** - Test restore secara berkala
6. **Disaster Recovery** - Prosedur restore yang terdokumentasi

### Tools yang Digunakan:
- **mysqldump** - Backup utility MySQL
- **gzip** - Kompresi untuk hemat storage
- **Task Scheduler/Cron** - Automasi backup
- **7-Zip** - Kompresi alternatif (Windows)

## ⚠️ Important Notes

1. **Password Security**: Jangan simpan password di script. Gunakan `.my.cnf` file:
   ```
   [mysqldump]
   user=root
   password=your_password
   ```

2. **Test Restore**: Selalu test restore backup secara berkala

3. **Off-site Backup**: Simpan backup di lokasi berbeda (cloud storage, external drive)

4. **Monitor Backup**: Setup alert jika backup gagal

5. **Document Recovery Time**: Catat berapa lama waktu restore untuk disaster recovery planning
