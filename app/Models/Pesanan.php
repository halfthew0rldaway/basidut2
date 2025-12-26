<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pesanan';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nomor_pesanan',
        'pelanggan_id',
        'total',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total' => 'decimal:2',
    ];

    /**
     * Get the customer that owns the order.
     */
    public function pelanggan()
    {
        return $this->belongsTo(Pengguna::class, 'pelanggan_id');
    }

    /**
     * Get the order items for the order.
     */
    public function itemPesanan()
    {
        return $this->hasMany(ItemPesanan::class, 'pesanan_id');
    }

    /**
     * Get the shipping information for the order.
     */
    public function pengiriman()
    {
        return $this->hasOne(Pengiriman::class, 'pesanan_id');
    }

    /**
     * Get the status badge color.
     *
     * @return string
     */
    public function getStatusBadgeClass()
    {
        return match ($this->status) {
            'menunggu' => 'warning',
            'dibayar' => 'info',
            'dikemas' => 'primary',
            'dikirim' => 'secondary',
            'selesai' => 'success',
            'dibatalkan' => 'danger',
            default => 'light',
        };
    }
}
