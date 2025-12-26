🚀 Project Context: Enterprise E-Commerce System
Target Database: basidut (MySQL) Framework: Laravel (Latest Version) Architecture: Monolithic with Stored Procedure Logic (Thin Controller, Fat Database).

⚠️ CRITICAL INSTRUCTIONS FOR AI AGENT
DO NOT HALLUCINATE SCHEMA. You must adhere strictly to the existing database schema defined below. The SQL script has already been executed.

1. Database Connection Rules
Database Name: basidut

Driver: mysql

Strict Mode: Ensure specific table names are used. Do not use standard Laravel naming conventions (e.g., use pengguna NOT users).

2. Schema Reference (Existing Tables)
pengguna (id, username, email, kata_sandi, nama_lengkap, aktif)

Note: Use this for Laravel Auth. Password field is kata_sandi, NOT password.

produk (id, nama, harga, sku, stok, kategori_id)

Constraint: CHECK (harga >= 0), CHECK (stok >= 0).

pesanan (id, nomor_pesanan, pelanggan_id, total, status)

Enum Status: 'menunggu', 'dibayar', 'dikemas', 'dikirim', 'selesai', 'dibatalkan'.

item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan)

pengiriman (pesanan_id, kurir, nomor_resi, status_pengiriman)

log_audit (System generated, read-only for App).

3. Business Logic Rules (Strict)
Creating Orders: You MUST NOT write logic to insert into pesanan and item_pesanan manually in PHP.

REQUIRED ACTION: You must call the Stored Procedure:

SQL

CALL sp_buat_pesanan_enterprise(user_id, product_id, qty, courier, address, @out_id, @out_status);
Reason: The SP handles ACID transactions, locking, stock validation, and logistics integration atomically.

🛠️ Step-by-Step Implementation Prompt
Prompt for AI: "Please generate the Laravel project code based on the instructions below."

Step 1: Laravel Model Configuration
Create Models that map to the Indonesian table names.

Example User Model (app/Models/Pengguna.php):

PHP

class Pengguna extends Authenticatable {
    protected $table = 'pengguna'; // Override default 'users'
    public $timestamps = false;    // DB uses 'dibuat_pada' timestamp default
    protected $fillable = ['username', 'email', 'kata_sandi', 'nama_lengkap'];
    
    public function getAuthPassword() {
        return $this->kata_sandi; // Override default 'password'
    }
}
Example Product Model (app/Models/Produk.php):

PHP

class Produk extends Model {
    protected $table = 'produk';
    public $timestamps = false;
}
Step 2: API Controller (OrderController)
Create a controller to handle the Checkout process.

Endpoint: POST /api/orders

Logic:

Accept JSON: { "product_id": 1, "qty": 2, "courier": "JNE", "address": "Home" }.

Get user_id from Auth.

Execute Raw Query: DB::statement("CALL sp_buat_pesanan_enterprise(?, ?, ?, ?, ?, @id, @status)", [...]).

Select output variables: SELECT @id, @status.

Return JSON response based on @status.

Step 3: Dummy Frontend (Web Interface)
Create a simple Blade view (resources/views/shop.blade.php) to demonstrate the "Tugas Besar" requirement:

Product List: Fetch from produk table.

Buy Button: Triggers the API or Form Submit to create an order.

Order History: Show data from v_monitoring_pengiriman (The View created in SQL).

✅ Definition of Done
User can login using data from pengguna table.

User can click "Buy" on a product.

Laravel calls sp_buat_pesanan_enterprise.

Database handles stock reduction and logging (Trigger).

User sees the new order in their history.