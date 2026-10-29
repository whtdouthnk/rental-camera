<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class LaporanController extends Controller
{
    /**
     * Halaman laporan transaksi dengan filter tanggal.
     */
    public function index(Request $request)
    {
        $dari = $request->filled('dari') ? Carbon::parse($request->dari)->startOfDay() : null;
        $sampai = $request->filled('sampai') ? Carbon::parse($request->sampai)->endOfDay() : null;

        if ($dari && $sampai && $sampai->lt($dari)) {
            return back()->withInput()->with('error', 'Tanggal "sampai" tidak boleh sebelum tanggal "dari"');
        }

        $transaksis = $this->query($dari, $sampai, $request->status)
            ->paginate(10)
            ->withQueryString();

        return view('laporan.index', compact('transaksis'));
    }

    /**
     * Cetak laporan transaksi berdasarkan filter tanggal.
     */
    public function cetak(Request $request)
    {
        $dari = $request->filled('dari') ? Carbon::parse($request->dari)->startOfDay() : null;
        $sampai = $request->filled('sampai') ? Carbon::parse($request->sampai)->endOfDay() : null;

        $transaksis = $this->query($dari, $sampai, $request->status)->get();

        $totalTransaksi = $transaksis->count();
        $totalSewa = $transaksis->sum(fn ($t) => $t->totalBiayaSewa());
        $totalDenda = $transaksis->sum(fn ($t) => $t->pengembalian->total_denda ?? 0);
        $totalPendapatan = $totalSewa + $totalDenda;

        return view('laporan.cetak', compact(
            'transaksis',
            'dari',
            'sampai',
            'totalTransaksi',
            'totalSewa',
            'totalDenda',
            'totalPendapatan'
        ));
    }

    private function query(?Carbon $dari, ?Carbon $sampai, ?string $status)
    {
        $query = Transaksi::with(['user', 'detailTransaksi.barang', 'pengembalian'])
            ->latest('tgl_pinjam');

        if ($dari) {
            $query->whereDate('tgl_pinjam', '>=', $dari->toDateString());
        }

        if ($sampai) {
            $query->whereDate('tgl_pinjam', '<=', $sampai->toDateString());
        }

        if ($status) {
            $query->where('status', $status);
        }

        return $query;
    }
}
