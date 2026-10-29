<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use Illuminate\Http\Request;

class LogAktivitasController extends Controller
{
    /**
     * Menampilkan daftar log aktivitas.
     */
    public function index(Request $request)
    {
        $query = LogAktivitas::with('user')
            ->latest();

        if ($request->filled('modul')) {
            $query->where('modul', $request->modul);
        }

        if ($request->filled('cari')) {
            $query->where('deskripsi', 'like', '%' . $request->cari . '%');
        }

        $logs = $query->paginate(20)->withQueryString();

        $moduls = LogAktivitas::select('modul')
            ->distinct()
            ->orderBy('modul')
            ->pluck('modul');

        return view('admin.log-aktivitas.index', compact('logs', 'moduls'));
    }
}
