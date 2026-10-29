<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $fillable = [
        'kode_transaksi',
        'user_id',
        'tgl_pinjam',
        'tgl_jatuh_tempo',
        'status',
        'disetujui_oleh',
        'catatan'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function detailTransaksi()
    {
        return $this->hasMany(DetailTransaksi::class);
    }

    public function pengembalian()
    {
        return $this->hasOne(Pengembalian::class);
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class);
    }

    /**
     * Lama sewa dalam hari (minimal 1 hari).
     */
    public function durasiHari(): int
    {
        $mulai = \Carbon\Carbon::parse($this->tgl_pinjam);
        $selesai = \Carbon\Carbon::parse($this->tgl_jatuh_tempo);

        return max(1, (int) $mulai->diffInDays($selesai));
    }

    /**
     * Total biaya sewa untuk seluruh barang yang dipinjam.
     */
    public function totalBiayaSewa(): float
    {
        return round(
            $this->durasiHari() * $this->detailTransaksi->sum(
                fn ($d) => (float) $d->barang->harga_sewa_per_hari * $d->jumlah
            ),
            2
        );
    }
}