<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ObatExport;
use App\Http\Controllers\Controller;
use App\Models\Obat;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ObatController extends Controller
{
    public function export()
    {
        return Excel::download(new ObatExport, 'data_obat_' . date('Ymd_His') . '.xlsx');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $obats = Obat::latest()->get();
        return view('admin.obat.index', compact('obats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.obat.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_obat' => 'required|string|max:255',
            'kemasan' => 'nullable|string|max:35',
            'harga' => 'required|numeric|min:0',
            'stok' => 'nullable|integer|min:0',
        ]);

        if (!isset($validated['stok']) || $validated['stok'] === null) {
            $validated['stok'] = 100;
        }

        Obat::create($validated);

        return redirect()
            ->route('obat.index')
            ->with('message', 'Data obat berhasil ditambahkan')
            ->with('type', 'success');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return redirect()->route('obat.edit', $id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $obat = Obat::findOrFail($id);
        return view('admin.obat.edit', compact('obat'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $obat = Obat::findOrFail($id);

        $validated = $request->validate([
            'nama_obat' => 'required|string|max:255',
            'kemasan' => 'nullable|string|max:35',
            'harga' => 'required|numeric|min:0',
            'stok' => 'nullable|integer|min:0',
        ]);

        if (!isset($validated['stok']) || $validated['stok'] === null) {
            $validated['stok'] = $obat->stok ?? 100;
        }

        $obat->update($validated);

        return redirect()
            ->route('obat.index')
            ->with('message', 'Data obat berhasil diperbarui')
            ->with('type', 'success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $obat = Obat::findOrFail($id);
        $obat->delete();

        return redirect()
            ->route('obat.index')
            ->with('message', 'Data obat berhasil dihapus')
            ->with('type', 'success');
    }
}
