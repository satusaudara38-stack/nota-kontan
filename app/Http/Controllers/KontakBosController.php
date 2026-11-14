<?php

namespace App\Http\Controllers;

use App\Models\KontakBos;
use Illuminate\Http\Request;

class KontakBosController extends Controller
{
    public function index()
    {
        $kontakBos = KontakBos::latest()->get();
        return view('kontak-bos.index', compact('kontakBos'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Hanya Administrator Utama yang berhak menambah kontak pimpinan.');
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:150',
            'nomor_wa' => 'required|string|max:30',
            'jabatan' => 'nullable|string|max:100',
        ]);

        $validated['is_active'] = true;

        KontakBos::create($validated);

        return redirect()
            ->route('kontak-bos.index')
            ->with('success', 'Kontak pimpinan baru berhasil ditambahkan.');
    }

    public function update(Request $request, KontakBos $kontakBos)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Hanya Administrator Utama yang berhak mengubah kontak pimpinan.');
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:150',
            'nomor_wa' => 'required|string|max:30',
            'jabatan' => 'nullable|string|max:100',
        ]);

        $kontakBos->update($validated);

        return redirect()
            ->route('kontak-bos.index')
            ->with('success', 'Data kontak pimpinan berhasil diperbarui.');
    }

    public function destroy(KontakBos $kontakBos)
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Hanya Administrator Utama yang berhak menghapus kontak pimpinan.');
        }

        $nama = $kontakBos->nama;
        $kontakBos->delete();

        return redirect()
            ->route('kontak-bos.index')
            ->with('success', "Kontak pimpinan {$nama} berhasil dihapus.");
    }
}
