@extends('layouts.app')

@section('title', 'Detail Pesanan - Basidut Shop')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('orders.index') }}" class="btn btn-light mb-3">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <h1 class="page-title">Detail Pesanan #{{ $pesanan->nomor_pesanan }}</h1>
            <p class="page-subtitle">Informasi lengkap pesanan Anda</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-box-seam"></i> Item Pesanan</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>SKU</th>
                                    <th>Harga</th>
                                    <th>Jumlah</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pesanan->itemPesanan as $item)
                                    <tr>
                                        <td>{{ $item->produk->nama }}</td>
                                        <td>{{ $item->produk->sku }}</td>
                                        <td>Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                        <td>{{ $item->jumlah }}</td>
                                        <td>Rp {{ number_format($item->getSubtotal(), 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">Total:</th>
                                    <th>Rp {{ number_format($pesanan->total, 0, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            @if($pesanan->pengiriman)
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-truck"></i> Informasi Pengiriman</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Kurir:</strong> {{ $pesanan->pengiriman->kurir }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>No. Resi:</strong> {{ $pesanan->pengiriman->nomor_resi ?? 'Belum tersedia' }}</p>
                            </div>
                            <div class="col-12">
                                <p><strong>Status Pengiriman:</strong>
                                    <span class="badge bg-info">{{ $pesanan->pengiriman->status_pengiriman }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Informasi Pesanan</h5>
                </div>
                <div class="card-body">
                    <p><strong>Nomor Pesanan:</strong><br>{{ $pesanan->nomor_pesanan }}</p>
                    <p><strong>Status:</strong><br>
                        <span class="badge bg-{{ $pesanan->getStatusBadgeClass() }}">
                            {{ ucfirst($pesanan->status) }}
                        </span>
                    </p>
                    <p><strong>Pelanggan:</strong><br>{{ $pesanan->pelanggan->nama_lengkap }}</p>
                    <p><strong>Email:</strong><br>{{ $pesanan->pelanggan->email }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection