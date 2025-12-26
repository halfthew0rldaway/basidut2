# Database Setup Guide

## ⚠️ Important Issue Found

The `basidut.sql` file has **plain text passwords** (`password123`), but Laravel requires **bcrypt hashed passwords**. This is why login fails even after importing the database.

## 🔧 Solution: Two Options

### Option 1: Import Database + Update Passwords (Recommended)

#### Step 1: Import the SQL File
```bash
# Using MySQL command line
mysql -u root -p < basidut.sql

# Or using HeidiSQL:
# 1. Open HeidiSQL
# 2. Connect to your MySQL server
# 3. File > Run SQL file > Select basidut.sql
# 4. Click Execute
```

#### Step 2: Update Passwords to Bcrypt Hash
After importing, run this SQL to update all passwords to bcrypt hashed version:

```sql
USE basidut;

-- Update all user passwords to bcrypt hash of 'password123'
-- Hash: $2y$12$bz3jN1tD/7AAwNFO6k2ln.jxnxuAC9A3qGkAiirQ4/fs2z9wE9/XK
UPDATE pengguna 
SET kata_sandi = '$2y$12$bz3jN1tD/7AAwNFO6k2ln.jxnxuAC9A3qGkAiirQ4/fs2z9wE9/XK';
```

#### Step 3: Verify
```sql
-- Check if passwords are updated
SELECT id, username, email, LEFT(kata_sandi, 20) as password_hash 
FROM pengguna 
LIMIT 5;
```

### Option 2: Use Laravel Seeder (Alternative)

Create a seeder to populate the database with proper bcrypt passwords:

```bash
php artisan make:seeder PenggunaSeeder
```

Then run:
```bash
php artisan db:seed --class=PenggunaSeeder
```

## ✅ After Setup

1. **Verify .env database connection:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=basidut
DB_USERNAME=root
DB_PASSWORD=your_mysql_password
```

2. **Clear config cache:**
```bash
php artisan config:clear
```

3. **Test login in Postman:**
   - URL: `POST http://127.0.0.1:8000/api/login`
   - Body (JSON):
   ```json
   {
       "email": "user1@mail.com",
       "kata_sandi": "password123"
   }
   ```

## 🔑 Test Accounts

After fixing passwords, you can login with:
- Email: `user1@mail.com` to `user100@mail.com`
- Password: `password123`

Or the admin account:
- Email: `basidut@jokowi.com`
- Password: `password123`
