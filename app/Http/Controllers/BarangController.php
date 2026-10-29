<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $barangs = Barang::with('kategori')->latest()->paginate(10);

        return view('admin.barang.index', compact('barangs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kategoris = Kategori::orderBy('nama_kategori')->get();

        return view('admin.barang.form', compact('kategoris'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'kategori_id'    => 'required|exists:kategori,id',
            'nama_barang'    => 'required|string|max:255',
            'merk'           => 'required|string|max:255',
            'serial_number'  => 'nullable|string|max:255',
            'stok'           => 'required|integer|min:0',
            'kondisi'        => 'required|in:Baik,Rusak Ringan,Rusak',
            'denda_per_hari' => 'required|numeric|min:0',
            'harga_sewa_per_hari' => 'required|numeric|min:0',
            'foto'           => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status'         => 'required|in:Aktif,Nonaktif'
        ]);

        $kategori = Kategori::findOrFail($data['kategori_id']);

        $data['kode_barang'] = Barang::generateKodeBarang($kategori->kode);
        $data['stok_tersedia'] = $data['stok'];

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('barang', 'public');
        }

        $barang = Barang::create($data);

        LogAktivitas::record('create', 'Barang', "Barang {$barang->nama_barang} (kode {$barang->kode_barang}) ditambahkan");

        return redirect()->route('admin.barang.index')
            ->with('success', 'Barang berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $barang = Barang::with('kategori')->findOrFail($id);
        $kategoris = Kategori::orderBy('nama_kategori')->get();

        return view('admin.barang.form', compact('barang', 'kategoris'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $barang = Barang::findOrFail($id);

        $stokLama = $barang->stok;

        $data = $request->validate([
            'kategori_id'    => 'required|exists:kategori,id',
            'nama_barang'    => 'required|string|max:255',
            'merk'           => 'required|string|max:255',
            'serial_number'  => 'nullable|string|max:255',
            'stok'           => 'required|integer|min:0',
            'kondisi'        => 'required|in:Baik,Rusak Ringan,Rusak',
            'denda_per_hari' => 'required|numeric|min:0',
            'harga_sewa_per_hari' => 'required|numeric|min:0',
            'foto'           => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status'         => 'required|in:Aktif,Nonaktif'
        ]);

        $data['stok_tersedia'] = max(0, $barang->stok_tersedia + ($data['stok'] - $stokLama));

        if ($request->hasFile('foto')) {
            if ($barang->foto) {
                Storage::disk('public')->delete($barang->foto);
            }

            $data['foto'] = $request->file('foto')->store('barang', 'public');
        }

        $barang->update($data);

        LogAktivitas::record('update', 'Barang', "Barang {$barang->nama_barang} diperbarui");

        return redirect()->route('admin.barang.index')
            ->with('success', 'Barang berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $barang = Barang::findOrFail($id);

        if ($barang->detailTransaksi()->exists()) {
            return redirect()->route('admin.barang.index')
                ->with('error', 'Barang tidak bisa dihapus karena sudah digunakan dalam transaksi');
        }

        if ($barang->foto) {
            Storage::disk('public')->delete($barang->foto);
        }

        $barang->delete();

        LogAktivitas::record('delete', 'Barang', "Barang {$barang->nama_barang} dihapus");

        return redirect()->route('admin.barang.index')
            ->with('success', 'Barang berhasil dihapus');
    }
}
