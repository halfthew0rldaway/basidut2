<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'produk';

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
        'nama',
        'harga',
        'sku',
        'stok',
        'kategori_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'harga' => 'decimal:2',
        'stok' => 'integer',
    ];

    /**
     * Get the order items for the product.
     */
    public function itemPesanan()
    {
        return $this->hasMany(ItemPesanan::class, 'produk_id');
    }

    /**
     * Check if product is in stock.
     *
     * @param int $quantity
     * @return bool
     */
    public function hasStock($quantity = 1)
    {
        return $this->stok >= $quantity;
    }
}
