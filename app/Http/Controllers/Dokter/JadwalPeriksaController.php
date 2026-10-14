<?php

namespace App\Http\Controllers\Dokter;

use App\Exports\JadwalPeriksaExport;
use App\Http\Controllers\Controller;
use App\Models\JadwalPeriksa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class JadwalPeriksaController extends Controller
{
    public function export()
    {
        return Excel::download(
            new JadwalPeriksaExport(Auth::id()),
            'jadwal_periksa_' . date('Ymd_His') . '.xlsx'
        );
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jadwals = JadwalPeriksa::where('id_dokter', Auth::id())->get();
        return view('dokter.jadwal-periksa.index', compact('jadwals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        return view('dokter.jadwal-periksa.create', compact('hariList'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ], [
            'jam_selesai.after' => 'Jam selesai harus setelah jam mulai.',
        ]);

        JadwalPeriksa::create([
            'id_dokter' => Auth::id(),
            'hari' => $request->hari,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
        ]);

        return redirect()
            ->route('jadwal-periksa.index')
            ->with('message', 'Jadwal periksa berhasil ditambahkan')
            ->with('type', 'success');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return redirect()->route('jadwal-periksa.edit', $id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $jadwal = JadwalPeriksa::where('id_dokter', Auth::id())->findOrFail($id);
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        return view('dokter.jadwal-periksa.edit', compact('jadwal', 'hariList'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $jadwal = JadwalPeriksa::where('id_dokter', Auth::id())->findOrFail($id);

        $request->validate([
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ], [
            'jam_selesai.after' => 'Jam selesai harus setelah jam mulai.',
        ]);

        $jadwal->update([
            'hari' => $request->hari,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
        ]);

        return redirect()
            ->route('jadwal-periksa.index')
            ->with('message', 'Jadwal periksa berhasil diperbarui')
            ->with('type', 'success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $jadwal = JadwalPeriksa::where('id_dokter', Auth::id())->findOrFail($id);
        $jadwal->delete();

        return redirect()
            ->route('jadwal-periksa.index')
            ->with('message', 'Jadwal periksa berhasil dihapus')
            ->with('type', 'success');
    }
}
