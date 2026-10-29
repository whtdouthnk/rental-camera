<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogAktivitasController;
use App\Http\Controllers\PengembalianController;
use App\Http\Controllers\PenggunaDashboardController;
use App\Http\Controllers\PetugasDashboardController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login',
    [LoginController::class,'index']
)->name('login');


Route::post('/login',
    [LoginController::class,'login']
)->name('login.process');


Route::post('/logout',
    [LoginController::class,'logout']
)->name('logout');


Route::middleware(['auth'])->group(function () {

    Route::middleware(['role:Admin'])->prefix('admin')->name('admin.')->group(function () {

        Route::get('/dashboard',
            [AdminDashboardController::class,'index']
        )->name('dashboard');

        Route::resource('user', UserController::class)
            ->except('show');

        Route::resource('barang', BarangController::class)
            ->except('show');

        Route::resource('kategori', KategoriController::class)
            ->except('show');

        Route::get('/transaksi',
            [TransaksiController::class,'index']
        )->name('transaksi.index');

        Route::get('/transaksi/{transaksi}',
            [TransaksiController::class,'show']
        )->name('transaksi.show');

        Route::get('/pengembalian',
            [PengembalianController::class,'index']
        )->name('pengembalian.index');

        Route::get('/pengembalian/{pengembalian}',
            [PengembalianController::class,'show']
        )->name('pengembalian.show');

        Route::get('/log-aktivitas',
            [LogAktivitasController::class,'index']
        )->name('log-aktivitas.index');

        Route::get('/laporan',
            [LaporanController::class,'index']
        )->name('laporan.index');

        Route::get('/laporan/cetak',
            [LaporanController::class,'cetak']
        )->name('laporan.cetak');

    });


    Route::middleware(['role:Petugas'])->prefix('petugas')->name('petugas.')->group(function () {

        Route::get('/dashboard',
            [PetugasDashboardController::class,'index']
        )->name('dashboard');

        Route::get('/transaksi',
            [TransaksiController::class,'index']
        )->name('transaksi.index');

        Route::get('/transaksi/{transaksi}',
            [TransaksiController::class,'show']
        )->name('transaksi.show');

        Route::post('/transaksi/{transaksi}/approve',
            [TransaksiController::class,'approve']
        )->name('transaksi.approve');

        Route::post('/transaksi/{transaksi}/reject',
            [TransaksiController::class,'reject']
        )->name('transaksi.reject');

        Route::post('/transaksi/{transaksi}/serah',
            [TransaksiController::class,'serah']
        )->name('transaksi.serah');

        Route::get('/transaksi/{transaksi}/bukti',
            [TransaksiController::class,'buktiPembayaran']
        )->name('transaksi.bukti');

        Route::get('/pengembalian',
            [PengembalianController::class,'index']
        )->name('pengembalian.index');

        Route::get('/pengembalian/create',
            [PengembalianController::class,'create']
        )->name('pengembalian.create');

        Route::post('/pengembalian',
            [PengembalianController::class,'store']
        )->name('pengembalian.store');

        Route::get('/pengembalian/{pengembalian}',
            [PengembalianController::class,'show']
        )->name('pengembalian.show');

    });


    Route::middleware(['role:Pengguna'])->prefix('user')->name('user.')->group(function () {

        Route::get('/dashboard',
            [PenggunaDashboardController::class,'index']
        )->name('dashboard');

        Route::get('/transaksi',
            [TransaksiController::class,'index']
        )->name('transaksi.index');

        Route::get('/transaksi/create',
            [TransaksiController::class,'create']
        )->name('transaksi.create');

        Route::post('/transaksi',
            [TransaksiController::class,'store']
        )->name('transaksi.store');

        Route::get('/transaksi/{transaksi}',
            [TransaksiController::class,'show']
        )->name('transaksi.show');

        Route::post('/transaksi/{transaksi}/cancel',
            [TransaksiController::class,'cancel']
        )->name('transaksi.cancel');

    });

});
