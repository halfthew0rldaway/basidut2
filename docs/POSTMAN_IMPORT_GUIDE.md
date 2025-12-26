# Postman Collection Import Guide

## 📥 How to Import

### Step 1: Open Postman
Launch Postman application on your computer.

### Step 2: Import Collection
1. Click **Import** button (top left)
2. Click **Upload Files**
3. Select `Basidut_API_Collection.postman_collection.json`
4. Click **Import**

### Step 3: Setup Environment (Optional but Recommended)
1. Click **Environments** (left sidebar)
2. Click **+** to create new environment
3. Name it: `Basidut Local`
4. Add variables:
   - `base_url` = `http://127.0.0.1:8000/api`
   - `jwt_token` = (leave empty, will be auto-filled)
5. Click **Save**
6. Select `Basidut Local` from environment dropdown

## 🧪 Testing Workflow

### Complete Test Sequence

**1. Start Laravel Server**
```bash
php artisan serve
```

**2. Run Tests in Order:**

#### A. Authentication Flow
1. **1.2 Login - Get JWT Token** ✅
   - This automatically saves the token
   - Check Console to see saved token
   
2. **1.3 Get Current User Profile** ✅
   - Verifies token works
   - Uses saved token automatically

#### B. Product CRUD (Tests Constraints)
3. **2.1 Get All Products** ✅
   - Should return 3 products
   
4. **2.3 Create Product** ✅
   - Tests INSERT with constraints
   - Tests FOREIGN KEY (kategori_id)
   - Tests UNIQUE constraint (sku)
   
5. **2.4 Update Product** ✅
   - Change ID to match created product
   - Tests UPDATE operation
   
6. **2.2 Get Single Product** ✅
   - Verify product details

#### C. Orders (Tests Stored Procedure & Transaction)
7. **3.1 Create Order - Test Stored Procedure** ✅
   - **CRITICAL**: Tests stored procedure
   - Tests ACID transaction
   - Tests row locking
   - Triggers audit log
   
8. **3.2 Create Order - Test Stock Validation** ✅
   - Should fail with "Stok Tidak Mencukupi"
   - Tests transaction ROLLBACK
   
9. **3.3 Get User's Orders** ✅
   - Tests JOIN queries
   - Should show created order

#### D. Advanced Features
10. **4.1 Shipping Monitoring (View)** ✅
    - Tests database VIEW
    - Shows joined data
    
11. **4.2 Audit Logs (Trigger)** ✅
    - Tests database TRIGGER
    - Should show stock changes from order creation

## 📊 What Each Test Validates

### Database Features Tested

| Test | Feature | TB Requirement |
|------|---------|----------------|
| 3.1 Create Order | Stored Procedure | ✅ sp_buat_pesanan_enterprise |
| 3.1 Create Order | Transaction | ✅ BEGIN/COMMIT/ROLLBACK |
| 4.2 Audit Logs | Trigger | ✅ trg_audit_stok_update |
| 4.1 Shipping Monitoring | View | ✅ v_monitoring_pengiriman |
| 3.3 Get Orders | JOIN Query | ✅ Multi-table JOIN |
| 2.3 Create Product | Constraints | ✅ CHECK, FK, UNIQUE |
| 1.2 Login | Bcrypt Password | ✅ Hash::make() |

### API Features Tested

| Test | HTTP Method | Feature |
|------|-------------|---------|
| 1.1 Register | POST | Create user |
| 1.2 Login | POST | JWT authentication |
| 1.3 Get Profile | GET | Protected endpoint |
| 2.1 Get Products | GET | Read operation |
| 2.3 Create Product | POST | Create with validation |
| 2.4 Update Product | PUT | Update operation |
| 2.5 Delete Product | DELETE | Delete operation |
| 3.1 Create Order | POST | Stored procedure call |

## ✅ Success Criteria

After running all tests, you should have:

1. **Authentication**
   - ✅ JWT token saved automatically
   - ✅ Can access protected endpoints

2. **Products**
   - ✅ Created new product
   - ✅ Updated product
   - ✅ Constraints validated

3. **Orders**
   - ✅ Order created via stored procedure
   - ✅ Stock reduced
   - ✅ Audit log created

4. **Advanced Features**
   - ✅ View returns shipping data
   - ✅ Trigger logged stock changes

## 🔍 Verification in Database

After running tests, verify in MySQL:

```sql
-- Check created order
SELECT * FROM pesanan ORDER BY id DESC LIMIT 1;

-- Check stock reduced
SELECT id, nama, stok FROM produk WHERE id = 1;

-- Check audit log (trigger)
SELECT * FROM log_audit ORDER BY id DESC LIMIT 5;

-- Check view works
SELECT * FROM v_monitoring_pengiriman;

-- Test function
SELECT hitung_total_pesanan(1) as total;
```

## 📝 Notes

- **Token Auto-Save**: Login request automatically saves JWT token to environment
- **Order**: Run tests in sequence for best results
- **IDs**: Update product/order IDs in URLs as needed
- **Stock**: Each order reduces stock, affecting future tests

## 🐛 Troubleshooting

### "Unauthenticated" Error
- Run **1.2 Login** again to refresh token
- Check token is saved in environment variables

### "Stok Tidak Mencukupi"
- This is expected for test 3.2
- For test 3.1, reduce qty or use different product

### "Table doesn't exist"
- Run `php artisan migrate:fresh --seed`
- Restart Laravel server

## 📚 Documentation

For detailed API documentation, see:
- `docs/API_DOCUMENTATION.md` - Complete API reference
- `docs/API_TESTING_GUIDE.md` - Detailed testing scenarios
- `docs/MIGRATION_GUIDE.md` - Database setup guide
