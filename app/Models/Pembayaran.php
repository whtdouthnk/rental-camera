<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';

    protected $fillable = [
        'transaksi_id',
        'jumlah',
        'metode',
        'dibayar_pada',
        'dibayar_oleh'
    ];

    protected $casts = [
        'jumlah'       => 'float',
        'dibayar_pada' => 'datetime'
    ];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'dibayar_oleh');
    }
}
