-- ============================================================================
-- FIX PASSWORD HASHING FOR BASIDUT DATABASE
-- ============================================================================
-- This script updates all plain text passwords to bcrypt hashed passwords
-- Run this AFTER importing basidut.sql
-- ============================================================================

USE basidut;

-- Bcrypt hash for 'password123'
-- Generated using: bcrypt('password123', 12)
-- Hash: $2y$12$bz3jN1tD/7AAwNFO6k2ln.jxnxuAC9A3qGkAiirQ4/fs2z9wE9/XK

-- Update all user passwords
UPDATE pengguna 
SET kata_sandi = '$2y$12$bz3jN1tD/7AAwNFO6k2ln.jxnxuAC9A3qGkAiirQ4/fs2z9wE9/XK'
WHERE kata_sandi = 'password123';

-- Verify the update
SELECT 
    COUNT(*) as total_users,
    SUM(CASE WHEN kata_sandi LIKE '$2y$%' THEN 1 ELSE 0 END) as hashed_passwords,
    SUM(CASE WHEN kata_sandi = 'password123' THEN 1 ELSE 0 END) as plain_passwords
FROM pengguna;

-- Show sample users
SELECT id, username, email, LEFT(kata_sandi, 30) as password_hash, aktif
FROM pengguna
LIMIT 10;

-- ============================================================================
-- RESULT: All passwords should now be bcrypt hashed
-- You can now login with:
-- - Email: user1@mail.com to user100@mail.com
-- - Password: password123
-- ============================================================================
