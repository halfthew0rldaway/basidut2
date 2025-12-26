@extends('layouts.app')

@section('title', 'Panduan Testing - Basidut Shop')

@push('styles')
    <style>
        .step-number {
            width: 40px;
            height: 40px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            margin-right: 12px;
        }

        .feature-icon {
            width: 48px;
            height: 48px;
            background: var(--bg-light);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: var(--primary);
        }
    </style>
@endpush

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="page-title">
                <i class="bi bi-book text-primary"></i> Panduan Testing Aplikasi
            </h1>
            <p class="page-subtitle">Petunjuk lengkap untuk menguji semua fitur sistem Basidut E-Commerce</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Overview -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-info-circle-fill me-2"></i> Tentang Sistem
                </div>
                <div class="card-body">
                    <p class="mb-3">Sistem Basidut E-Commerce ini dibangun dengan arsitektur <strong>Monolithic
                            Database</strong> yang mengintegrasikan fitur-fitur database advanced untuk memastikan
                        integritas data dan performa optimal.</p>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <div class="feature-icon me-3">
                                    <i class="bi bi-database"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Stored Procedure</h6>
                                    <small class="text-muted">Transaksi ACID dengan locking mechanism</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <div class="feature-icon me-3">
                                    <i class="bi bi-lightning"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Database Trigger</h6>
                                    <small class="text-muted">Audit logging otomatis</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <div class="feature-icon me-3">
                                    <i class="bi bi-eye"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Database View</h6>
                                    <small class="text-muted">Monitoring pengiriman real-time</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <div class="feature-icon me-3">
                                    <i class="bi bi-shield-check"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Custom Auth</h6>
                                    <small class="text-muted">Tabel Indonesian dengan kata_sandi</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Test Credentials -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-person-badge"></i> Akun Testing
                </div>
                <div class="card-body">
                    <p>Sistem sudah memiliki <strong>100 akun dummy</strong> yang siap digunakan:</p>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Password</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>user1</code> - <code>user100</code></td>
                                    <td><code>user1@mail.com</code> - <code>user100@mail.com</code></td>
                                    <td><code>password123</code></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-lightbulb"></i> <strong>Contoh:</strong> Login dengan email
                        <code>user1@mail.com</code> dan password <code>password123</code>
                    </div>
                </div>
            </div>

            <!-- Testing Steps -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-list-check me-2"></i> Langkah-Langkah Testing
                </div>
                <div class="card-body">
                    <!-- Step 1 -->
                    <div class="d-flex align-items-start mb-4">
                        <span class="step-number">1</span>
                        <div class="flex-grow-1">
                            <h5 class="mb-2">Test Autentikasi</h5>
                            <ol class="mb-0">
                                <li>Klik menu <strong>Register</strong> untuk membuat akun baru</li>
                                <li>Isi semua field: username, email, password, nama lengkap</li>
                                <li>Setelah berhasil, otomatis login dan redirect ke <code>/shop</code></li>
                                <li>Coba logout dan login kembali dengan kredensial yang sama</li>
                            </ol>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="d-flex align-items-start mb-4">
                        <span class="step-number">2</span>
                        <div class="flex-grow-1">
                            <h5 class="mb-2">Test Product Listing</h5>
                            <ol class="mb-0">
                                <li>Buka halaman <strong>Produk</strong> dari menu utama</li>
                                <li>Verifikasi 3 produk tampil dengan benar:
                                    <ul>
                                        <li><strong>Laptop Pro</strong> - Rp 15.000.000 (Stok: 50)</li>
                                        <li><strong>Smartphone X</strong> - Rp 8.000.000 (Stok: 100)</li>
                                        <li><strong>Kemeja Kantor</strong> - Rp 150.000 (Stok: 200)</li>
                                    </ul>
                                </li>
                                <li>Pastikan informasi harga dan stok tampil dengan format yang benar</li>
                            </ol>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="d-flex align-items-start mb-4">
                        <span class="step-number">3</span>
                        <div class="flex-grow-1">
                            <h5 class="mb-2">Test Order Creation <span class="badge bg-warning text-dark">Stored
                                    Procedure</span></h5>
                            <ol class="mb-0">
                                <li>Login terlebih dahulu (gunakan akun test atau buat baru)</li>
                                <li>Klik tombol <strong>"Beli Sekarang"</strong> pada produk pilihan</li>
                                <li>Modal form akan muncul, isi data:
                                    <ul>
                                        <li><strong>Jumlah:</strong> 1-5 unit (sesuai stok tersedia)</li>
                                        <li><strong>Kurir:</strong> Pilih JNE, JNT, SiCepat, AnterAja, atau Pos Indonesia
                                        </li>
                                        <li><strong>Alamat:</strong> Masukkan alamat lengkap pengiriman</li>
                                    </ul>
                                </li>
                                <li>Klik <strong>"Buat Pesanan"</strong></li>
                                <li>Sistem akan memanggil <code>sp_buat_pesanan_enterprise</code></li>
                                <li>Toast notification akan muncul dengan nomor pesanan</li>
                                <li>Otomatis redirect ke halaman <strong>Pesanan</strong></li>
                            </ol>
                        </div>
                    </div>

                    <!-- Step 4 -->
                    <div class="d-flex align-items-start mb-4">
                        <span class="step-number">4</span>
                        <div class="flex-grow-1">
                            <h5 class="mb-2">Test Order History</h5>
                            <ol class="mb-0">
                                <li>Klik menu <strong>Pesanan</strong> di navigation bar</li>
                                <li>Verifikasi pesanan yang baru dibuat muncul di tabel</li>
                                <li>Data diambil dari query JOIN (atau view jika sudah dibuat)</li>
                                <li>Klik tombol <strong>"Detail"</strong> untuk melihat informasi lengkap</li>
                                <li>Periksa item pesanan, total harga, dan informasi pengiriman</li>
                            </ol>
                        </div>
                    </div>

                    <!-- Step 5 -->
                    <div class="d-flex align-items-start">
                        <span class="step-number">5</span>
                        <div class="flex-grow-1">
                            <h5 class="mb-2">Verifikasi Database <span class="badge bg-info">HeidiSQL</span></h5>
                            <p>Setelah membuat pesanan, cek perubahan di database:</p>
                            <pre class="bg-light p-3 rounded border"><code>-- Cek pesanan baru
    SELECT * FROM pesanan ORDER BY id DESC LIMIT 5;

    -- Cek item pesanan
    SELECT * FROM item_pesanan ORDER BY id DESC LIMIT 5;

    -- Cek pengiriman
    SELECT * FROM pengiriman ORDER BY id DESC LIMIT 5;

    -- Cek stok produk (harus berkurang)
    SELECT nama, stok FROM produk;

    -- Cek audit log (trigger otomatis)
    SELECT * FROM log_audit ORDER BY id DESC LIMIT 10;

    -- (Optional) Jika view sudah dibuat
    SELECT * FROM v_monitoring_pengiriman LIMIT 10;</code></pre>
                            <div class="alert alert-warning mb-0">
                                <i class="bi bi-info-circle-fill me-2"></i> <strong>Catatan:</strong> View
                                <code>v_monitoring_pengiriman</code> bersifat opsional.
                                Sistem tetap berfungsi tanpa view karena menggunakan query JOIN langsung.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Expected Results -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-check-circle"></i> Hasil yang Diharapkan
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Fitur</th>
                                    <th>Hasil</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Register</td>
                                    <td>Data tersimpan di tabel <code>pengguna</code></td>
                                </tr>
                                <tr>
                                    <td>Login</td>
                                    <td>Session dibuat, redirect ke /shop</td>
                                </tr>
                                <tr>
                                    <td>Buat Pesanan</td>
                                    <td>
                                        • Record baru di <code>pesanan</code><br>
                                        • Record baru di <code>item_pesanan</code><br>
                                        • Record baru di <code>pengiriman</code><br>
                                        • Stok berkurang otomatis (trigger)<br>
                                        • Log audit tercatat (trigger)
                                    </td>
                                </tr>
                                <tr>
                                    <td>Order History</td>
                                    <td>Data dari view <code>v_monitoring_pengiriman</code></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Links -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-link-45deg"></i> Quick Links
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @guest
                            <a href="{{ route('login') }}" class="btn btn-primary">
                                <i class="bi bi-box-arrow-in-right"></i> Login
                            </a>
                            <a href="{{ route('register') }}" class="btn btn-light">
                                <i class="bi bi-person-plus"></i> Register
                            </a>
                        @endguest
                        <a href="{{ route('shop') }}" class="btn btn-light">
                            <i class="bi bi-shop"></i> Lihat Produk
                        </a>
                        @auth
                            <a href="{{ route('orders.index') }}" class="btn btn-light">
                                <i class="bi bi-receipt"></i> Pesanan Saya
                            </a>
                        @endauth
                    </div>
                </div>
            </div>

            <!-- Technical Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-gear"></i> Informasi Teknis
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Database:</strong> basidut (MySQL)</p>
                    <p class="mb-2"><strong>Framework:</strong> Laravel</p>
                    <p class="mb-2"><strong>Authentication:</strong> Custom (pengguna table)</p>
                    <p class="mb-0"><strong>Password Field:</strong> kata_sandi</p>
                </div>
            </div>

            <!-- Features -->
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-star"></i> Fitur Advanced
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success"></i> ACID Transaction
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success"></i> Stored Procedure
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success"></i> Database Trigger
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success"></i> Database View
                        </li>
                        <li class="mb-0">
                            <i class="bi bi-check-circle text-success"></i> Audit Logging
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection