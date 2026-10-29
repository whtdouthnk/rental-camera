<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Barang;
use App\Models\Transaksi;
use App\Models\Pengembalian;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // jumlah user
        $totalUser = User::count();


        // jumlah barang
        $totalBarang = Barang::count();


        // barang yang tersedia
        // $barangTersedia = Barang::sum('stok_tersedia');


        // transaksi aktif
        $transaksiAktif = Transaksi::whereIn('status', [
            'Disetujui',
            'Dipinjam'
        ])->count();


        // transaksi menunggu persetujuan
        $menunggu = Transaksi::where(
            'status',
            'Menunggu'
        )->count();


        // total denda
        $totalDenda = Pengembalian::sum('total_denda');


        // transaksi terbaru
        $transaksiTerbaru = Transaksi::with('user')
            ->latest()
            ->limit(5)
            ->get();


        // user terbaru
        $userTerbaru = User::latest()
            ->limit(5)
            ->get();


        return view('admin.dashboard', compact(
            'totalUser',
            'totalBarang',
            // 'barangTersedia',
            'transaksiAktif',
            'menunggu',
            'totalDenda',
            'transaksiTerbaru',
            'userTerbaru'
        ));
    }
}