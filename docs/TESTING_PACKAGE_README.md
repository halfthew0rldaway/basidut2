# Basidut API - Complete Testing Package

## 📦 Files Created

### Postman Collection
**File**: `docs/Basidut_API_Collection.postman_collection.json`

Import this into Postman to get all test requests ready to use!

### Documentation
1. **POSTMAN_IMPORT_GUIDE.md** - How to import and use the collection
2. **API_TESTING_GUIDE.md** - Detailed testing scenarios
3. **API_DOCUMENTATION.md** - Complete API reference
4. **MIGRATION_GUIDE.md** - Database setup instructions

## 🚀 Quick Start

### 1. Import to Postman
```
File → Import → Upload Files → Select Basidut_API_Collection.postman_collection.json
```

### 2. Run Tests in Order

The collection includes **15 requests** organized in 5 folders:

#### 1️⃣ Authentication (4 requests)
- Register New User
- **Login - Get JWT Token** (auto-saves token!)
- Get Current User Profile
- Logout

#### 2️⃣ Products - CRUD (5 requests)
- Get All Products
- Get Single Product
- Create Product (tests constraints)
- Update Product
- Delete Product

#### 3️⃣ Orders - Stored Procedure (4 requests)
- **Create Order** - Tests stored procedure with ACID transaction
- Create Order - Test stock validation (should fail)
- Get User's Orders (JOIN query)
- Get Single Order Details

#### 4️⃣ Advanced Features (2 requests)
- **Shipping Monitoring** - Tests database VIEW
- **Audit Logs** - Tests database TRIGGER

#### 5️⃣ Health Check (1 request)
- API Health Check

## ✅ What Gets Tested

### Database Features (TB Requirements)
- ✅ **Stored Procedure**: `sp_buat_pesanan_enterprise`
- ✅ **Trigger**: `trg_audit_stok_update`
- ✅ **Function**: `hitung_total_pesanan` (used internally)
- ✅ **View**: `v_monitoring_pengiriman`
- ✅ **Transaction**: BEGIN/COMMIT/ROLLBACK
- ✅ **JOIN Queries**: Multi-table joins
- ✅ **Constraints**: CHECK, FOREIGN KEY, UNIQUE
- ✅ **Indexes**: Performance optimization

### API Features
- ✅ JWT Authentication
- ✅ CRUD Operations (3 modules: Produk, Pesanan, Pengguna)
- ✅ Protected Endpoints
- ✅ Request Validation
- ✅ Error Handling

## 📋 Testing Checklist

Before testing:
- [ ] Run `php artisan migrate:fresh --seed`
- [ ] Start server: `php artisan serve`
- [ ] Import Postman collection

Test sequence:
1. [ ] Login (saves token automatically)
2. [ ] Get products
3. [ ] Create product
4. [ ] Create order (tests stored procedure)
5. [ ] Check audit logs (tests trigger)
6. [ ] Check shipping monitoring (tests view)

## 🎯 Key Tests for TB

### Test 1: Stored Procedure + Transaction
**Request**: `3.1 Create Order - Test Stored Procedure`

Tests:
- ACID transaction (BEGIN/COMMIT)
- Row locking (FOR UPDATE)
- Multi-table insert
- Stock validation
- Error handling with ROLLBACK

### Test 2: Trigger
**Request**: `4.2 Audit Logs (Trigger)`

After creating order, this shows:
- Automatic audit logging
- Stock change tracking
- Trigger execution proof

### Test 3: View
**Request**: `4.1 Shipping Monitoring (View)`

Shows:
- Database view usage
- Multi-table JOIN
- Real-time data aggregation

### Test 4: JOIN Queries
**Request**: `3.3 Get User's Orders`

Uses complex JOIN:
```sql
pesanan 
  LEFT JOIN pengiriman
  LEFT JOIN item_pesanan
  LEFT JOIN produk
```

## 📊 Expected Results

After running all tests:

**Database Changes:**
- New product created (ID: 4)
- New order created
- Stock reduced (e.g., Laptop Pro: 50 → 48)
- Audit log entries (2-3 entries)

**API Responses:**
- All requests return proper JSON
- JWT token auto-saved
- Protected endpoints work with token
- Validation errors handled properly

## 🔍 Verify in Database

```sql
-- Check order created
SELECT * FROM pesanan ORDER BY id DESC LIMIT 1;

-- Check stock reduced
SELECT nama, stok FROM produk WHERE id = 1;

-- Check audit log (trigger proof)
SELECT * FROM log_audit ORDER BY id DESC;

-- Test view
SELECT * FROM v_monitoring_pengiriman;

-- Test function
SELECT hitung_total_pesanan(1);
```

## 📝 Notes

- **Auto Token Save**: Login request automatically saves JWT token
- **Test Order**: Run requests in sequence for best results
- **Stock Tracking**: Each order reduces stock
- **Audit Logs**: Trigger creates entries automatically

## 🎓 For TB Presentation

You can demonstrate:
1. **Stored Procedure** - Show order creation in Postman
2. **Trigger** - Show audit logs populated automatically
3. **View** - Show shipping monitoring data
4. **Function** - Run in database: `SELECT hitung_total_pesanan(1)`
5. **Transaction** - Show rollback on stock validation failure
6. **JOIN** - Show complex query results in orders endpoint

All advanced database features are working and testable via API! 🚀
