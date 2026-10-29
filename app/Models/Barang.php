<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';

    protected $fillable = [
        'kategori_id',
        'kode_barang',
        'nama_barang',
        'merk',
        'serial_number',
        'stok',
        'stok_tersedia',
        'kondisi',
        'denda_per_hari',
        'harga_sewa_per_hari',
        'foto',
        'status'
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function detailTransaksi()
    {
        return $this->hasMany(DetailTransaksi::class);
    }

    public static function generateKodeBarang(string $kategoriKode): string
    {
        $last = self::where('kode_barang', 'like', $kategoriKode . '-%')
            ->orderByRaw('CAST(SUBSTRING(kode_barang, LOCATE("-", kode_barang) + 1) AS UNSIGNED) DESC')
            ->value('kode_barang');

        $next = 1;

        if ($last) {
            $parts = explode('-', $last);
            $next = ((int) end($parts)) + 1;
        }

        return $kategoriKode . '-' . str_pad($next, 3, '0', STR_PAD_LEFT);
    }
}