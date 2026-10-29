<?php

namespace App\Models;

use App\Models\Barang;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategori';

    protected $fillable = [
        'kode',
        'nama_kategori'
    ];

    public function barang()
    {
        return $this->hasMany(Barang::class);
    }

    public static function generateKode(string $nama): string
    {
        $base = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $nama), 0, 3));

        if ($base === '') {
            $base = 'KT';
        }

        $kode = $base;
        $i = 1;

        while (self::where('kode', $kode)->exists()) {
            $i++;
            $kode = $base . $i;
        }

        return $kode;
    }
}