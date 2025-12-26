# Basidut API - Quick Reference

## 🚀 API Endpoints Summary

### Public Endpoints (No Auth)
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/register` | Register new user |
| POST | `/api/login` | Login and get JWT token |
| GET | `/api/produk` | Get all products |
| GET | `/api/produk/{id}` | Get single product |
| GET | `/api/health` | Health check |

### Protected Endpoints (Requires JWT)
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/me` | Get current user profile |
| POST | `/api/logout` | Logout |
| POST | `/api/produk` | Create product |
| PUT | `/api/produk/{id}` | Update product |
| DELETE | `/api/produk/{id}` | Delete product |
| GET | `/api/pesanan` | Get user's orders |
| GET | `/api/pesanan/{id}` | Get order details |
| POST | `/api/pesanan` | Create order (stored procedure) |
| GET | `/api/monitoring-pengiriman` | Get shipping monitoring |
| GET | `/api/audit-logs` | Get audit logs |

## 📝 Quick Test Commands

### 1. Health Check
```bash
curl http://127.0.0.1:8000/api/health
```

### 2. Login
```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -d "{\"email\":\"user1@mail.com\",\"kata_sandi\":\"password123\"}"
```

### 3. Get Products
```bash
curl http://127.0.0.1:8000/api/produk
```

### 4. Get Profile (with token)
```bash
curl http://127.0.0.1:8000/api/me \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### 5. Create Order (with token)
```bash
curl -X POST http://127.0.0.1:8000/api/pesanan \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d "{\"product_id\":1,\"qty\":1,\"courier\":\"JNE\",\"address\":\"Jl. Test\"}"
```

## 🔑 Test Accounts
- Email: `user1@mail.com` to `user100@mail.com`
- Password: `password123`

## 📚 Full Documentation
See [API_DOCUMENTATION.md](./API_DOCUMENTATION.md) for complete details.
