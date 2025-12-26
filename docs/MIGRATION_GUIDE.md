# Migration Guide - Basidut Database

## 🚀 Quick Start

### Step 1: Delete Old Database (if exists)
```sql
DROP DATABASE IF EXISTS basidut;
CREATE DATABASE basidut CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Or in HeidiSQL:
- Right-click on `basidut` database → Drop
- Create new database named `basidut`

### Step 2: Update .env File
Make sure your `.env` has correct database settings:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=basidut
DB_USERNAME=root
DB_PASSWORD=your_mysql_password

JWT_SECRET=your_jwt_secret_here
```

### Step 3: Run Fresh Migration with Seeding
```bash
php artisan migrate:fresh --seed
```

This single command will:
- Drop all existing tables
- Run all migrations (create tables, stored procedure, trigger, function, view)
- Seed the database with test data

### Step 4: Verify Setup
```bash
php artisan tinker
```

Then run these checks:
```php
// Check user count (should be 101)
App\Models\Pengguna::count();

// Check password is bcrypt hashed
App\Models\Pengguna::first()->kata_sandi; // Should start with $2y$

// Check products
App\Models\Produk::count(); // Should be 3

// Check stored procedure exists
DB::select("SHOW PROCEDURE STATUS WHERE Db = 'basidut'");

// Check trigger exists
DB::select("SHOW TRIGGERS WHERE `Table` = 'produk'");

// Check view exists
DB::select("SHOW FULL TABLES WHERE Table_Type = 'VIEW'");
```

## 📋 What Gets Created

### Tables (8)
1. **kategori** - Product categories
2. **pengguna** - Users with bcrypt passwords
3. **produk** - Products with foreign key to kategori
4. **pesanan** - Orders with foreign key to pengguna
5. **item_pesanan** - Order items (many-to-many)
6. **pengiriman** - Shipping information
7. **log_audit** - Audit logs
8. **metode_pembayaran** - Payment methods

### Advanced Features (4)
1. **Stored Procedure**: `sp_buat_pesanan_enterprise` - Creates orders with ACID transaction
2. **Trigger**: `trg_audit_stok_update` - Auto-logs stock changes
3. **Function**: `hitung_total_pesanan` - Calculates order total
4. **View**: `v_monitoring_pengiriman` - Shipping monitoring

### Seeded Data
- **101 users** (user1-user100 + admin) - Password: `password123` (bcrypt hashed)
- **3 categories** (Elektronik, Fashion, Rumah Tangga)
- **3 products** (Laptop Pro, Smartphone X, Kemeja Kantor)
- **2 payment methods** (Transfer Bank, Kartu Kredit)

## 🧪 Testing the Setup

### Test 1: Login API
```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user1@mail.com","kata_sandi":"password123"}'
```

Expected: JWT token returned

### Test 2: Get Products
```bash
curl http://127.0.0.1:8000/api/produk
```

Expected: 3 products returned

### Test 3: Create Order (Tests Stored Procedure)
First login to get token, then:
```bash
curl -X POST http://127.0.0.1:8000/api/pesanan \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"product_id":1,"qty":1,"courier":"JNE","address":"Test Address"}'
```

Expected: Order created successfully

### Test 4: Check Audit Log (Tests Trigger)
```bash
php artisan tinker
```
```php
DB::table('log_audit')->get();
```

Expected: Audit entry for stock change

### Test 5: Test Function
```bash
php artisan tinker
```
```php
DB::select("SELECT hitung_total_pesanan(1) as total");
```

### Test 6: Test View
```bash
php artisan tinker
```
```php
DB::table('v_monitoring_pengiriman')->get();
```

## 🔄 Common Commands

### Fresh Migration (Drops Everything)
```bash
php artisan migrate:fresh --seed
```

### Rollback Last Migration
```bash
php artisan migrate:rollback
```

### Check Migration Status
```bash
php artisan migrate:status
```

### Run Seeders Only
```bash
php artisan db:seed
```

### Run Specific Seeder
```bash
php artisan db:seed --class=PenggunaSeeder
```

## 🔑 Test Credentials

**Regular Users:**
- Email: `user1@mail.com` to `user100@mail.com`
- Password: `password123`

**Admin:**
- Email: `basidut@jokowi.com`
- Password: `password123`

## ⚠️ Troubleshooting

### Error: "Access denied for user"
- Check `.env` DB_USERNAME and DB_PASSWORD
- Make sure MySQL is running

### Error: "Database doesn't exist"
- Create database manually: `CREATE DATABASE basidut;`

### Error: "Syntax error in migration"
- Make sure MySQL version is 8.0+
- Check that all migrations are in correct order

### Passwords not working
- Make sure seeders ran successfully
- Check password is bcrypt: `App\Models\Pengguna::first()->kata_sandi`
- Should start with `$2y$`

## 📊 Database Schema Overview

```
kategori (1) ──< produk (N)
                   │
                   │ (N)
                   ↓
pengguna (1) ──< pesanan (N) ──< item_pesanan (N)
                   │
                   │ (1)
                   ↓
              pengiriman (1)
```

## ✅ Success Checklist

After running migrations, verify:
- [ ] All 8 tables created
- [ ] Stored procedure `sp_buat_pesanan_enterprise` exists
- [ ] Trigger `trg_audit_stok_update` exists
- [ ] Function `hitung_total_pesanan` exists
- [ ] View `v_monitoring_pengiriman` exists
- [ ] 101 users seeded with bcrypt passwords
- [ ] 3 products seeded
- [ ] API login works
- [ ] Can create orders via API
- [ ] Audit log records stock changes
