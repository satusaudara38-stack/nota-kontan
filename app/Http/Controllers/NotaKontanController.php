<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\NotaKontan;
use App\Models\KontakBos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NotaKontanController extends Controller
{
    public function dashboard(Request $request)
    {
        $query = NotaKontan::query();

        if ($request->filled('tanggal_dari')) {
            $query->where('tanggal_upload', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->where('tanggal_upload', '<=', $request->tanggal_sampai);
        }

        $totalNota = (clone $query)->count();
        $totalNominal = (float) (clone $query)->sum('nominal_pembayaran');

        // Transaksi Bulan Berjalan
        $transaksiBulanIni = NotaKontan::whereMonth('tanggal_upload', now()->month)
            ->whereYear('tanggal_upload', now()->year)
            ->count();

        $nominalBulanIni = (float) NotaKontan::whereMonth('tanggal_upload', now()->month)
            ->whereYear('tanggal_upload', now()->year)
            ->sum('nominal_pembayaran');

        // Rata-rata per transaksi
        $rataRata = $totalNota > 0 ? (int) ($totalNominal / $totalNota) : 0;

        // 5 Transaksi Paling Baru (sesuai filter)
        $recentNotas = (clone $query)->latest('tanggal_upload')->orderBy('id', 'desc')->take(5)->get();
        $bosses = KontakBos::where('is_active', true)->get();
        $totalBosses = $bosses->count();

        $filterAktif = $request->filled('tanggal_dari') || $request->filled('tanggal_sampai');

        return view('nota.dashboard', compact(
            'totalNota',
            'totalNominal',
            'transaksiBulanIni',
            'nominalBulanIni',
            'rataRata',
            'recentNotas',
            'bosses',
            'totalBosses',
            'filterAktif'
        ));
    }

    public function index(Request $request)
    {
        $query = NotaKontan::latest('tanggal_upload')->orderBy('id', 'desc');

        if ($request->filled('tanggal_dari')) {
            $query->where('tanggal_upload', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->where('tanggal_upload', '<=', $request->tanggal_sampai);
        }
        if ($request->filled('cari')) {
            $cari = $request->cari;
            $query->where(function ($q) use ($cari) {
                $q->where('nama_customer', 'like', '%' . $cari . '%')
                  ->orWhere('nama_barang', 'like', '%' . $cari . '%');
            });
        }

        $notas = $query->paginate(10)->withQueryString();
        $bosses = KontakBos::where('is_active', true)->get();
        $filterAktif = $request->filled('tanggal_dari')
            || $request->filled('tanggal_sampai')
            || $request->filled('cari');

        return view('nota.index', compact('notas', 'bosses', 'filterAktif'));
    }

    public function create()
    {
        return view('nota.create');
    }

   public function store(Request $request)
{
    $validated = $request->validate([
        'nama_customer' => 'required|string|max:255',
        'nama_barang' => 'required|string|max:255',
        'nominal_pembayaran' => 'required|numeric|min:0',
        'tanggal_upload' => 'required|date',
        'nomor_wa' => 'required|string|max:20',
        'bukti_pembayaran' => 'required|image|mimes:jpg,jpeg,png,webp,heic,heif|max:20480',
        
    ]);

    // Simpan bukti pembayaran ke storage
    $file = $request->file('bukti_pembayaran');

    $path = $file->store('nota-kontan', 'public');

    // Isi kolom transaksi
    $validated['bukti_pembayaran'] = $path;
    $validated['nominal_pembayaran'] = (int) $validated['nominal_pembayaran'];

    // Isi kolom wajib dari struktur database lama
    $validated['nama_file'] = $file->getClientOriginalName();
    $validated['path_file'] = $path;

    // Simpan transaksi
    NotaKontan::create($validated);

    return redirect()
        ->route('nota.index')
        ->with('success', 'Data nota kontan berhasil disimpan.');
}

    public function show(NotaKontan $nota)
    {
        $bosses = KontakBos::where('is_active', true)->get();
        return view('nota.show', compact('nota', 'bosses'));
    }

    public function downloadPdf(Request $request, NotaKontan $nota)
    {
        $namaFile = preg_replace(
            '/[^A-Za-z0-9_\-]/',
            '_',
            trim($nota->nama_customer ?? 'Customer')
        );

        // Jika parameter ?download=1 diberikan, unduh file PDF fisik
        if ($request->boolean('download')) {
            $pdf = Pdf::loadView('nota.pdf', compact('nota'));
            return $pdf->download($namaFile . '.pdf');
        }

        // Jika parameter ?raw=1 diberikan, kirim raw stream PDF biner
        if ($request->boolean('raw')) {
            $pdf = Pdf::loadView('nota.pdf', compact('nota'));
            return $pdf->stream($namaFile . '.pdf', [
                'Attachment' => 0,
            ]);
        }

        // Default: Tampilkan Lembar Dokumen Nota Resmi Digital (Bisa dibuka 100% langsung di semua jenis HP tanpa perlu download)
        return view('nota.preview', compact('nota'));
    }
    public function share(NotaKontan $nota)
    {
        $url = $nota->wa_customer_url;
        if (!$url) {
            return redirect()
                ->route('nota.index')
                ->with('error', 'Nomor WhatsApp customer belum tersedia.');
        }

        return redirect()->away($url);
    }

    public function shareBoss(Request $request, NotaKontan $nota)
    {
        $bossId = (int) $request->query('boss', 0);
        $boss = KontakBos::find($bossId) ?? KontakBos::where('is_active', true)->first();

        if (!$boss || empty($boss->nomor_wa)) {
            return redirect()
                ->route('nota.index')
                ->with('error', 'Kontak WhatsApp bos belum terdaftar. Silakan tambahkan di menu Kontak Pimpinan.');
        }

        return redirect()->away($nota->getWaBossUrl($boss));
    }
public function destroy(NotaKontan $nota)
{
    // Hapus file bukti pembayaran dari storage
    if ($nota->bukti_pembayaran &&
        \Illuminate\Support\Facades\Storage::disk('public')
            ->exists($nota->bukti_pembayaran)) {

        \Illuminate\Support\Facades\Storage::disk('public')
            ->delete($nota->bukti_pembayaran);
    }

    // Hapus data transaksi dari database
    $nota->delete();

    return redirect()
        ->route('nota.index')
        ->with('success', 'Data nota kontan berhasil dihapus.');
}
public function edit(NotaKontan $nota)
{
    return view('nota.edit', compact('nota'));
}

public function update(Request $request, NotaKontan $nota)
{
    $validated = $request->validate([
        'nama_customer' => 'required|string|max:255',
        'nomor_wa' => 'nullable|string|max:20',
        'nama_barang' => 'required|string|max:255',
        'nominal_pembayaran' => 'required|numeric|min:0',
        'tanggal_upload' => 'required|date',
        'bukti_pembayaran' =>
            'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
    ]);

    $data = [
        'nama_customer' => $validated['nama_customer'],
        'nama_barang' => $validated['nama_barang'],
        'nominal_pembayaran' => (int) $validated['nominal_pembayaran'],
        'tanggal_upload' => $validated['tanggal_upload'],
        'nomor_wa' => $validated['nomor_wa'] ?? null,
    ];

    if ($request->hasFile('bukti_pembayaran')) {
        // Hapus file bukti pembayaran lama dari storage jika ada
        if ($nota->bukti_pembayaran &&
            Storage::disk('public')->exists($nota->bukti_pembayaran)) {
            Storage::disk('public')->delete($nota->bukti_pembayaran);
        }

        $file = $request->file('bukti_pembayaran');
        $path = $file->store('nota-kontan', 'public');

        $data['bukti_pembayaran'] = $path;
        $data['nama_file'] = $file->getClientOriginalName();
        $data['path_file'] = $path;
    }

    $nota->update($data);

    return redirect()
        ->route('nota.index')
        ->with('success', 'Nota kontan berhasil diperbarui.');
}
}