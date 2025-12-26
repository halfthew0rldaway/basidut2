@extends('layouts.app')

@section('title', 'Shop - Basidut E-Commerce')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h1 class="page-title">Katalog Produk</h1>
        <p class="page-subtitle">Pilih produk yang Anda inginkan dan beli dengan mudah</p>
    </div>
</div>

<div class="row g-4">
    @forelse($produk as $item)
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h5 class="card-title mb-0 flex-grow-1">{{ $item->nama }}</h5>
                        <span class="badge bg-primary ms-2">{{ $item->sku }}</span>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex align-items-center text-muted mb-2">
                            <i class="bi bi-box-seam me-2"></i>
                            <span>Stok: <strong class="text-dark">{{ $item->stok }}</strong> unit</span>
                        </div>
                    </div>
                    
                    <h3 class="text-primary mb-3" style="font-weight: 700;">
                        Rp {{ number_format($item->harga, 0, ',', '.') }}
                    </h3>

                    <div class="mt-auto">
                        @auth
                            <button class="btn btn-primary w-100 btn-buy" 
                                    data-product-id="{{ $item->id }}"
                                    data-product-name="{{ $item->nama }}"
                                    data-product-price="{{ $item->harga }}"
                                    data-product-stock="{{ $item->stok }}">
                                <i class="bi bi-cart-plus me-1"></i> Beli Sekarang
                            </button>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-secondary w-100">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Login untuk Membeli
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-inbox display-1 text-muted"></i>
                    <h3 class="mt-3">Tidak ada produk tersedia</h3>
                    <p class="text-muted">Silakan cek kembali nanti</p>
                </div>
            </div>
        </div>
    @endforelse
</div>

<!-- Order Modal -->
<div class="modal fade" id="orderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-cart-check"></i> Buat Pesanan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="orderForm">
                    @csrf
                    <input type="hidden" id="product_id" name="product_id">

                    <div class="mb-3">
                        <label class="form-label">Produk</label>
                        <input type="text" class="form-control" id="product_name" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="qty" class="form-label">Jumlah</label>
                        <input type="number" class="form-control" id="qty" name="qty" min="1" value="1" required>
                        <small class="text-muted">Stok tersedia: <span id="stock_info"></span></small>
                    </div>

                    <div class="mb-3">
                        <label for="courier" class="form-label">Kurir</label>
                        <select class="form-select" id="courier" name="courier" required>
                            <option value="">Pilih Kurir</option>
                            <option value="JNE">JNE</option>
                            <option value="JNT">JNT</option>
                            <option value="SiCepat">SiCepat</option>
                            <option value="AnterAja">AnterAja</option>
                            <option value="Pos Indonesia">Pos Indonesia</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Alamat Pengiriman</label>
                        <textarea class="form-control" id="address" name="address" rows="3" required
                            placeholder="Masukkan alamat lengkap"></textarea>
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Total: Rp <span id="total_price">0</span>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="submitOrder">
                    <i class="bi bi-check-circle"></i> Buat Pesanan
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const orderModal = new bootstrap.Modal(document.getElementById('orderModal'));
            let currentProduct = {};

            // Handle buy button click
            document.querySelectorAll('.btn-buy').forEach(btn => {
                btn.addEventListener('click', function () {
                    currentProduct = {
                        id: this.dataset.productId,
                        name: this.dataset.productName,
                        price: parseFloat(this.dataset.productPrice),
                        stock: parseInt(this.dataset.productStock)
                    };

                    document.getElementById('product_id').value = currentProduct.id;
                    document.getElementById('product_name').value = currentProduct.name;
                    document.getElementById('stock_info').textContent = currentProduct.stock;
                    document.getElementById('qty').max = currentProduct.stock;
                    updateTotal();

                    orderModal.show();
                });
            });

            // Update total when quantity changes
            document.getElementById('qty').addEventListener('input', updateTotal);

            function updateTotal() {
                const qty = parseInt(document.getElementById('qty').value) || 0;
                const total = qty * currentProduct.price;
                document.getElementById('total_price').textContent = total.toLocaleString('id-ID');
            }

            // Submit order
            document.getElementById('submitOrder').addEventListener('click', function () {
                const form = document.getElementById('orderForm');
                const formData = new FormData(form);
                const submitBtn = this;

                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Memproses...';

                fetch('{{ route("orders.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        product_id: formData.get('product_id'),
                        qty: formData.get('qty'),
                        courier: formData.get('courier'),
                        address: formData.get('address')
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            orderModal.hide();

                            // Show success toast
                            showToast(`${data.message} - Nomor Pesanan: ${data.order_id}`, 'success');

                            // Redirect after 2 seconds
                            setTimeout(() => {
                                window.location.href = '{{ route("orders.index") }}';
                            }, 2000);
                        } else {
                            showToast(data.message, 'danger');
                        }
                    })
                    .catch(error => {
                        showToast('Terjadi kesalahan: ' + error.message, 'danger');
                    })
                    .finally(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="bi bi-check-circle"></i> Buat Pesanan';
                    });
            });
        });
    </script>
@endpush