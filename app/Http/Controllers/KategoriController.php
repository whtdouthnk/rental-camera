<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kategoris = Kategori::withCount('barang')->latest()->paginate(10);

        return view('admin.kategori.index', compact('kategoris'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.kategori.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori,nama_kategori'
        ]);

        $data['kode'] = Kategori::generateKode($data['nama_kategori']);

        $kategori = Kategori::create($data);

        LogAktivitas::record('create', 'Kategori', "Kategori {$kategori->nama_kategori} (kode {$kategori->kode}) ditambahkan");

        return redirect()->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $kategori = Kategori::findOrFail($id);

        return view('admin.kategori.form', compact('kategori'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $kategori = Kategori::findOrFail($id);

        $data = $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori,nama_kategori,' . $kategori->id
        ]);

        $kategori->update($data);

        LogAktivitas::record('update', 'Kategori', "Kategori {$kategori->nama_kategori} diperbarui");

        return redirect()->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $kategori = Kategori::findOrFail($id);

        if ($kategori->barang()->exists()) {
            return redirect()->route('admin.kategori.index')
                ->with('error', 'Kategori tidak bisa dihapus karena masih memiliki barang');
        }

        $kategori->delete();

        LogAktivitas::record('delete', 'Kategori', "Kategori {$kategori->nama_kategori} dihapus");

        return redirect()->route('admin.kategori.index')
            ->with('success', 'Kategori berhasil dihapus');
    }
}
