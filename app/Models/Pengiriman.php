<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengiriman extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pengiriman';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'pesanan_id';

    /**
     * Indicates if the model has an incrementing primary key.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'pesanan_id',
        'kurir',
        'nomor_resi',
        'status_pengiriman',
    ];

    /**
     * Get the order that owns the shipping.
     */
    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }
}
