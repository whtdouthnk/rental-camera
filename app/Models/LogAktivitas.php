<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LogAktivitas extends Model
{
    use HasFactory;

    protected $table = 'log_aktivitas';

    protected $fillable = [
        'user_id',
        'aksi',
        'modul',
        'deskripsi',
        'ip_address'
    ];

    /**
     * Catat aktivitas pengguna ke log.
     */
    public static function record(string $aksi, string $modul, string $deskripsi, ?User $user = null): void
    {
        self::create([
            'user_id'    => $user?->id ?? auth()->id(),
            'aksi'       => $aksi,
            'modul'      => $modul,
            'deskripsi'  => $deskripsi,
            'ip_address' => request()->ip()
        ]);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
