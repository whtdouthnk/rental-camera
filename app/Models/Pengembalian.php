<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pengembalian extends Model
{
    use HasFactory;

    protected $table = 'pengembalian';

    protected $fillable = [
        'transaksi_id',
        'tgl_kembali',
        'terlambat_hari',
        'total_denda',
        'kondisi_barang',
        'catatan',
        'diterima_oleh'
    ];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'diterima_oleh');
    }
}