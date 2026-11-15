<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\NotaKontan;
use App\Models\KontakBos;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NotaKontanController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = auth()->user();
        $query = NotaKontan::query();

        // Jika peran adalah Sales, batasi metrik hanya untuk data yang diinput oleh sales tersebut
        if ($user && $user->isSales()) {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('tanggal_dari')) {
            $query->where('tanggal_upload', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->where('tanggal_upload', '<=', $request->tanggal_sampai);
        }

        $totalNota = (clone $query)->count();
        $totalNominal = (float) (clone $query)->sum('nominal_pembayaran');

        // Transaksi Bulan Berjalan
        $bulanQuery = NotaKontan::whereMonth('tanggal_upload', now()->month)
            ->whereYear('tanggal_upload', now()->year);
        if ($user && $user->isSales()) {
            $bulanQuery->where('user_id', $user->id);
        }

        $transaksiBulanIni = (clone $bulanQuery)->count();
        $nominalBulanIni = (float) (clone $bulanQuery)->sum('nominal_pembayaran');

        // Rata-rata per transaksi
        $rataRata = $totalNota > 0 ? (int) ($totalNominal / $totalNota) : 0;

        // 5 Transaksi Paling Baru (sesuai filter & hak akses)
        $recentNotas = (clone $query)->with('user')->latest('tanggal_upload')->orderBy('id', 'desc')->take(5)->get();
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
        $user = auth()->user();
        $query = NotaKontan::with('user')->latest('tanggal_upload')->orderBy('id', 'desc');

        // Jika peran adalah Sales, hanya tampilkan nota miliknya sendiri
        if ($user && $user->isSales()) {
            $query->where('user_id', $user->id);
        } elseif ($request->filled('sales_id')) {
            // Administrator Utama & Admin Kantor bisa memfilter berdasarkan Sales pembuat
            $query->where('user_id', $request->sales_id);
        }

        if ($request->filled('tanggal_dari')) {
            $query->where('tanggal_upload', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->where('tanggal_upload', '<=', $request->tanggal_sampai);
        }
        if ($request->filled('kategori')) {
            if ($request->kategori === 'sparepart') {
                $query->where('kategori_transaksi', 'sparepart');
            } elseif ($request->kategori === 'mobil') {
                $query->where(function ($q) {
                    $q->where('kategori_transaksi', 'mobil')
                      ->orWhereNull('kategori_transaksi');
                });
            }
        }
        if ($request->filled('cari')) {
            $cari = $request->cari;
            $query->where(function ($q) use ($cari) {
                $q->where('nama_customer', 'like', '%' . $cari . '%')
                  ->orWhere('nama_barang', 'like', '%' . $cari . '%')
                  ->orWhere('type_unit', 'like', '%' . $cari . '%');
            });
        }

        $notas = $query->paginate(10)->withQueryString();
        $bosses = KontakBos::where('is_active', true)->get();
        $salesList = User::where('role', 'sales')->orderBy('name')->get();

        $filterAktif = $request->filled('tanggal_dari')
            || $request->filled('tanggal_sampai')
            || $request->filled('cari')
            || $request->filled('sales_id')
            || $request->filled('kategori');

        return view('nota.index', compact('notas', 'bosses', 'salesList', 'filterAktif'));
    }

    public function create()
    {
        return view('nota.create');
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $isSales = $user && $user->isSales();
        $kategori = $isSales ? 'mobil' : $request->input('kategori_transaksi', 'mobil');
        if (!in_array($kategori, ['mobil', 'sparepart'])) {
            $kategori = 'mobil';
        }

        // ==========================================
        // ALUR INPUT KHUSUS: SPAREPART & SERVIS
        // ==========================================
        if ($kategori === 'sparepart') {
            $validated = $request->validate([
                'nama_customer' => 'required|string|max:255',
                'nomor_wa' => 'required|string|max:25',
                'tanggal_upload' => 'required|date',
                'bukti_pembayaran' => 'required|image|mimes:jpg,jpeg,png,webp,heic,heif|max:20480',
                'total_diskon' => 'nullable|numeric|min:0',
                'nominal_pembayaran' => 'nullable|numeric|min:0',
                'items' => 'required|array|min:1',
                'items.*.nama' => 'required|string|max:255',
                'items.*.qty' => 'required|numeric|min:1',
                'items.*.harga' => 'required|numeric|min:0',
            ]);

            $items = [];
            $computedTotal = 0;
            foreach ($request->items as $item) {
                $nama = trim($item['nama'] ?? '');
                if ($nama === '') {
                    continue;
                }
                $qty = max(1, (int) ($item['qty'] ?? 1));
                $harga = max(0, (int) ($item['harga'] ?? 0));
                $subtotal = $qty * $harga;
                $computedTotal += $subtotal;

                $items[] = [
                    'nama' => $nama,
                    'qty' => $qty,
                    'harga' => $harga,
                    'subtotal' => $subtotal,
                ];
            }

            if (empty($items)) {
                return back()->withErrors(['items' => 'Minimal harus memasukkan 1 rincian sparepart/barang yang valid.'])->withInput();
            }

            $totalDiskon = (int) ($request->input('total_diskon', 0) ?: 0);
            $totalBayar = max(0, $computedTotal - $totalDiskon);
            if ($totalBayar === 0 && !empty($validated['nominal_pembayaran'])) {
                $totalBayar = (int) $validated['nominal_pembayaran'];
            }

            $firstItem = $items[0]['nama'];
            $moreCount = count($items) - 1;
            $namaBarangSummary = $firstItem . ($moreCount > 0 ? " (+{$moreCount} item)" : '');

            // Simpan bukti transfer / struk
            $file = $request->file('bukti_pembayaran');
            $path = $file->store('nota-kontan', 'public');

            NotaKontan::create([
                'user_id' => auth()->id(),
                'kategori_transaksi' => 'sparepart',
                'nama_customer' => $validated['nama_customer'],
                'nomor_wa' => $validated['nomor_wa'],
                'nama_stnk_bpkb' => $validated['nama_customer'],
                'type_unit' => 'Sparepart & Servis',
                'warna' => '-',
                'nama_barang' => $namaBarangSummary,
                'items' => $items,
                'nominal_pembayaran' => $totalBayar,
                'total_diskon' => $totalDiskon,
                'cara_pembelian' => 'Cash',
                'nama_leasing' => '-',
                'tenor' => '-',
                'tahun_mobil' => date('Y'),
                'tanggal_upload' => $validated['tanggal_upload'],
                'bukti_pembayaran' => $path,
                'nama_file' => $file->getClientOriginalName(),
                'path_file' => $path,
            ]);

            return redirect()
                ->route('nota.index')
                ->with('success', 'Transaksi penjualan sparepart & servis berhasil dicatat.');
        }

        // ==========================================
        // ALUR INPUT: PENJUALAN UNIT MOBIL (DO)
        // ==========================================
        $validated = $request->validate([
            'nama_customer' => 'required|string|max:255',
            'nomor_wa' => 'required|string|max:25',
            'nama_stnk_bpkb' => 'nullable|string|max:255',
            'type_unit' => 'required|string|max:255',
            'warna' => 'required|string|max:100',
            'nominal_pembayaran' => 'required|numeric|min:0', // Total Bayar
            'total_diskon' => 'nullable|numeric|min:0',
            'cara_pembelian' => 'required|string|in:Credit,Cash',
            'nama_leasing' => 'nullable|string|max:100',
            'tenor' => 'nullable|string|max:50',
            'tahun_mobil' => 'required|string|max:10',
            'tanggal_upload' => 'required|date',
            'bukti_pembayaran' => 'required|image|mimes:jpg,jpeg,png,webp,heic,heif|max:20480',
            'nama_barang' => 'nullable|string|max:255',
            'items' => 'nullable|array',
            'items.*.nama' => 'nullable|string|max:255',
            'items.*.qty' => 'nullable|numeric|min:1',
            'items.*.harga' => 'nullable|numeric|min:0',
        ]);

        if (empty($validated['nama_stnk_bpkb'])) {
            $validated['nama_stnk_bpkb'] = $validated['nama_customer'];
        }

        if ($validated['cara_pembelian'] === 'Cash') {
            $validated['nama_leasing'] = '-';
            $validated['tenor'] = '-';
        }

        $validated['kategori_transaksi'] = 'mobil';
        $validated['total_diskon'] = (int) ($request->input('total_diskon', 0) ?: 0);
        $totalNominal = (int) $validated['nominal_pembayaran'];
        $namaBarangSummary = $validated['type_unit'] . ' (' . $validated['warna'] . ')';

        // Proses rincian item jika ada
        $items = [];
        if ($request->has('items') && is_array($request->items)) {
            foreach ($request->items as $item) {
                $nama = trim($item['nama'] ?? '');
                if ($nama === '') {
                    continue;
                }
                $qty = max(1, (int) ($item['qty'] ?? 1));
                $harga = max(0, (int) ($item['harga'] ?? 0));
                $subtotal = $qty * $harga;

                $items[] = [
                    'nama' => $nama,
                    'qty' => $qty,
                    'harga' => $harga,
                    'subtotal' => $subtotal,
                ];
            }
        }

        if (empty($items)) {
            $items = [
                [
                    'nama' => $validated['type_unit'] . ' - ' . $validated['warna'] . ' (' . $validated['tahun_mobil'] . ')',
                    'qty' => 1,
                    'harga' => $totalNominal,
                    'subtotal' => $totalNominal,
                ]
            ];
        }

        // Simpan bukti pembayaran ke storage
        $file = $request->file('bukti_pembayaran');
        $path = $file->store('nota-kontan', 'public');

        // Hubungkan nota dengan akun pembuat (Sales atau Admin Kantor)
        $validated['user_id'] = auth()->id();
        $validated['bukti_pembayaran'] = $path;
        $validated['nama_barang'] = $namaBarangSummary;
        $validated['nominal_pembayaran'] = $totalNominal;
        $validated['items'] = $items;
        $validated['nama_file'] = $file->getClientOriginalName();
        $validated['path_file'] = $path;

        // Simpan transaksi
        NotaKontan::create($validated);

        return redirect()
            ->route('nota.index')
            ->with('success', 'Data transaksi pembelian mobil / delivery order berhasil disimpan.');
    }

    public function show(NotaKontan $nota)
    {
        $user = auth()->user();

        // Sales hanya boleh melihat nota yang diterbitkan oleh dirinya sendiri
        if ($user && $user->isSales() && $nota->user_id && $nota->user_id !== $user->id) {
            abort(403, 'Akses Ditolak: Anda hanya memiliki izin untuk melihat nota transaksi milik Anda sendiri.');
        }

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

        // Default: Tampilkan Lembar Dokumen Nota Resmi Digital (Bisa dibuka langsung di semua jenis HP tanpa download)
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
        // Hanya Administrator Utama yang berhak menghapus data transaksi
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Akses Ditolak: Hanya Administrator Utama yang memiliki wewenang untuk menghapus nota transaksi demi keamanan pembukuan.');
        }

        // Hapus file bukti pembayaran dari storage
        if ($nota->bukti_pembayaran &&
            Storage::disk('public')->exists($nota->bukti_pembayaran)) {
            Storage::disk('public')->delete($nota->bukti_pembayaran);
        }

        // Hapus data transaksi dari database
        $nota->delete();

        return redirect()
            ->route('nota.index')
            ->with('success', 'Data nota kontan berhasil dihapus.');
    }

    public function edit(NotaKontan $nota)
    {
        $user = auth()->user();

        // Sales hanya boleh mengedit nota miliknya sendiri
        if ($user && $user->isSales() && $nota->user_id && $nota->user_id !== $user->id) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk mengedit nota milik staf lain.');
        }

        return view('nota.edit', compact('nota'));
    }

    public function update(Request $request, NotaKontan $nota)
    {
        $user = auth()->user();

        // Sales hanya boleh mengedit nota miliknya sendiri
        if ($user && $user->isSales() && $nota->user_id && $nota->user_id !== $user->id) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk mengedit nota milik staf lain.');
        }

        $kategori = $nota->kategori_transaksi ?: 'mobil';

        // UPDATE KHUSUS SPAREPART
        if ($kategori === 'sparepart') {
            $validated = $request->validate([
                'nama_customer' => 'required|string|max:255',
                'nomor_wa' => 'required|string|max:25',
                'tanggal_upload' => 'required|date',
                'bukti_pembayaran' => 'nullable|image|mimes:jpg,jpeg,png,webp,heic,heif|max:20480',
                'total_diskon' => 'nullable|numeric|min:0',
                'nominal_pembayaran' => 'nullable|numeric|min:0',
                'items' => 'required|array|min:1',
                'items.*.nama' => 'required|string|max:255',
                'items.*.qty' => 'required|numeric|min:1',
                'items.*.harga' => 'required|numeric|min:0',
            ]);

            $items = [];
            $computedTotal = 0;
            foreach ($request->items as $item) {
                $nama = trim($item['nama'] ?? '');
                if ($nama === '') continue;
                $qty = max(1, (int) ($item['qty'] ?? 1));
                $harga = max(0, (int) ($item['harga'] ?? 0));
                $subtotal = $qty * $harga;
                $computedTotal += $subtotal;

                $items[] = [
                    'nama' => $nama,
                    'qty' => $qty,
                    'harga' => $harga,
                    'subtotal' => $subtotal,
                ];
            }

            if (empty($items)) {
                return back()->withErrors(['items' => 'Minimal harus memasukkan 1 rincian sparepart yang valid.'])->withInput();
            }

            $totalDiskon = (int) ($request->input('total_diskon', 0) ?: 0);
            $totalBayar = max(0, $computedTotal - $totalDiskon);
            if ($totalBayar === 0 && !empty($validated['nominal_pembayaran'])) {
                $totalBayar = (int) $validated['nominal_pembayaran'];
            }

            $firstItem = $items[0]['nama'];
            $moreCount = count($items) - 1;
            $namaBarangSummary = $firstItem . ($moreCount > 0 ? " (+{$moreCount} item)" : '');

            $data = [
                'nama_customer' => $validated['nama_customer'],
                'nomor_wa' => $validated['nomor_wa'],
                'nama_stnk_bpkb' => $validated['nama_customer'],
                'type_unit' => 'Sparepart & Servis',
                'warna' => '-',
                'nama_barang' => $namaBarangSummary,
                'items' => $items,
                'nominal_pembayaran' => $totalBayar,
                'total_diskon' => $totalDiskon,
                'cara_pembelian' => 'Cash',
                'nama_leasing' => '-',
                'tenor' => '-',
                'tanggal_upload' => $validated['tanggal_upload'],
            ];

            if ($request->hasFile('bukti_pembayaran')) {
                if ($nota->bukti_pembayaran && Storage::disk('public')->exists($nota->bukti_pembayaran)) {
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
                ->with('success', 'Data transaksi penjualan sparepart berhasil diperbarui.');
        }

        // UPDATE KHUSUS MOBIL
        $validated = $request->validate([
            'nama_customer' => 'required|string|max:255',
            'nomor_wa' => 'required|string|max:25',
            'nama_stnk_bpkb' => 'nullable|string|max:255',
            'type_unit' => 'required|string|max:255',
            'warna' => 'required|string|max:100',
            'nominal_pembayaran' => 'required|numeric|min:0', // Total Bayar
            'total_diskon' => 'nullable|numeric|min:0',
            'cara_pembelian' => 'required|string|in:Credit,Cash',
            'nama_leasing' => 'nullable|string|max:100',
            'tenor' => 'nullable|string|max:50',
            'tahun_mobil' => 'required|string|max:10',
            'tanggal_upload' => 'required|date',
            'bukti_pembayaran' => 'nullable|image|mimes:jpg,jpeg,png,webp,heic,heif|max:20480',
            'nama_barang' => 'nullable|string|max:255',
            'items' => 'nullable|array',
            'items.*.nama' => 'nullable|string|max:255',
            'items.*.qty' => 'nullable|numeric|min:1',
            'items.*.harga' => 'nullable|numeric|min:0',
        ]);

        if (empty($validated['nama_stnk_bpkb'])) {
            $validated['nama_stnk_bpkb'] = $validated['nama_customer'];
        }

        if ($validated['cara_pembelian'] === 'Cash') {
            $validated['nama_leasing'] = '-';
            $validated['tenor'] = '-';
        }

        $totalDiskon = (int) ($request->input('total_diskon', 0) ?: 0);
        $totalNominal = (int) $validated['nominal_pembayaran'];
        $namaBarangSummary = $validated['type_unit'] . ' (' . $validated['warna'] . ')';

        // Proses rincian item dinamis
        $items = [];
        if ($request->has('items') && is_array($request->items)) {
            foreach ($request->items as $item) {
                $nama = trim($item['nama'] ?? '');
                if ($nama === '') {
                    continue;
                }
                $qty = max(1, (int) ($item['qty'] ?? 1));
                $harga = max(0, (int) ($item['harga'] ?? 0));
                $subtotal = $qty * $harga;

                $items[] = [
                    'nama' => $nama,
                    'qty' => $qty,
                    'harga' => $harga,
                    'subtotal' => $subtotal,
                ];
            }
        }

        if (empty($items)) {
            $items = [
                [
                    'nama' => $validated['type_unit'] . ' - ' . $validated['warna'] . ' (' . $validated['tahun_mobil'] . ')',
                    'qty' => 1,
                    'harga' => $totalNominal,
                    'subtotal' => $totalNominal,
                ]
            ];
        }

        $data = [
            'nama_customer' => $validated['nama_customer'],
            'nama_stnk_bpkb' => $validated['nama_stnk_bpkb'],
            'type_unit' => $validated['type_unit'],
            'warna' => $validated['warna'],
            'nama_barang' => $namaBarangSummary,
            'nominal_pembayaran' => $totalNominal,
            'total_diskon' => $totalDiskon,
            'cara_pembelian' => $validated['cara_pembelian'],
            'nama_leasing' => $validated['nama_leasing'] ?? '-',
            'tenor' => $validated['tenor'] ?? '-',
            'tahun_mobil' => $validated['tahun_mobil'],
            'items' => $items,
            'tanggal_upload' => $validated['tanggal_upload'],
            'nomor_wa' => $validated['nomor_wa'] ?? null,
        ];

        if ($request->hasFile('bukti_pembayaran')) {
            if ($nota->bukti_pembayaran && Storage::disk('public')->exists($nota->bukti_pembayaran)) {
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
            ->with('success', 'Data transaksi pembelian mobil / nota kontan berhasil diperbarui.');
    }
}
