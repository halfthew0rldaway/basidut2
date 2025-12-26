@extends('layouts.app')

@section('title', 'Pesanan Saya - Basidut Shop')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="page-title">Pesanan Saya</h1>
            <p class="page-subtitle">Lihat dan kelola pesanan Anda</p>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if($orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No. Pesanan</th>
                                        <th>Produk</th>
                                        <th>Jumlah</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Kurir</th>
                                        <th>No. Resi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                        <tr>
                                            <td><strong>{{ $order->nomor_pesanan }}</strong></td>
                                            <td>{{ $order->nama_produk ?? 'N/A' }}</td>
                                            <td>{{ $order->jumlah ?? 'N/A' }}</td>
                                            <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                            <td>
                                                @php
                                                    $badgeClass = match ($order->status) {
                                                        'menunggu' => 'warning',
                                                        'dibayar' => 'info',
                                                        'dikemas' => 'primary',
                                                        'dikirim' => 'secondary',
                                                        'selesai' => 'success',
                                                        'dibatalkan' => 'danger',
                                                        default => 'light',
                                                    };
                                                @endphp
                                                <span class="badge bg-{{ $badgeClass }}">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $order->kurir ?? '-' }}</td>
                                            <td>{{ $order->nomor_resi ?? '-' }}</td>
                                            <td>
                                                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-eye"></i> Detail
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox display-1 text-muted"></i>
                            <h3 class="mt-3">Belum ada pesanan</h3>
                            <p class="text-muted">Mulai berbelanja sekarang!</p>
                            <a href="{{ route('shop') }}" class="btn btn-primary">
                                <i class="bi bi-shop"></i> Lihat Produk
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection