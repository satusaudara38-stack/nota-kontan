@extends('layouts.app')

@section('title', 'Edit Nota #' . str_pad($nota->id, 5, '0', STR_PAD_LEFT))

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-8">
        {{-- Breadcrumb --}}
        <div class="d-flex align-items-center gap-2 mb-3">
            <a href="{{ route('nota.index') }}" class="btn btn-outline-secondary btn-sm px-2.5 py-1.5" title="Kembali ke Daftar">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="h5 fw-bold text-dark mb-0">Edit Nota Kontan #{{ str_pad($nota->id, 5, '0', STR_PAD_LEFT) }}</h1>
                <span class="text-muted small">Perbarui rincian transaksi atau foto ulang struk</span>
            </div>
        </div>

        <div class="card card-custom p-4 p-md-5 shadow-sm">
            <form action="{{ route('nota.update', $nota->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    {{-- Nama Customer --}}
                    <div class="col-md-6">
                        <label for="nama_customer" class="form-label small fw-semibold text-dark">
                            Nama Customer <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               id="nama_customer"
                               name="nama_customer"
                               class="form-control @error('nama_customer') is-invalid @enderror"
                               value="{{ old('nama_customer', $nota->nama_customer) }}"
                               required
                               maxlength="150">
                        @error('nama_customer')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Nomor WhatsApp --}}
                    <div class="col-md-6">
                        <label for="nomor_wa" class="form-label small fw-semibold text-dark">
                            Nomor WhatsApp Customer
                        </label>
                        <input type="tel"
                               id="nomor_wa"
                               name="nomor_wa"
                               class="form-control @error('nomor_wa') is-invalid @enderror"
                               value="{{ old('nomor_wa', $nota->nomor_wa) }}"
                               placeholder="Contoh: 081234567890"
                               maxlength="20">
                        @error('nomor_wa')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tanggal Transaksi --}}
                    <div class="col-12">
                        <label for="tanggal_upload" class="form-label small fw-semibold text-dark">
                            Tanggal Transaksi <span class="text-danger">*</span>
                        </label>
                        <input type="date"
                               id="tanggal_upload"
                               name="tanggal_upload"
                               class="form-control @error('tanggal_upload') is-invalid @enderror"
                               value="{{ old('tanggal_upload', $nota->tanggal_upload?->format('Y-m-d')) }}"
                               required>
                        @error('tanggal_upload')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Rincian Barang / Item Transaksi (Bisa Tambah & Atur Jumlah Item) --}}
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <label class="form-label small fw-semibold text-dark mb-0">
                                    Rincian Barang / Jasa <span class="text-danger">*</span>
                                </label>
                                <span class="text-muted d-block" style="font-size: 0.73rem;">
                                    Atur atau sesuaikan barang/jasa, kuantitas, dan harga satuan
                                </span>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm py-1.5 px-3 d-inline-flex align-items-center gap-1.5" id="btnTambahItem" style="font-size: 0.78rem;">
                                <i class="bi bi-plus-circle"></i>
                                <span>Tambah Item Baru</span>
                            </button>
                        </div>

                        <div class="card bg-light border p-2.5 p-md-3 rounded-3 shadow-none">
                            <div id="itemsContainer" class="d-flex flex-column gap-2">
                                @foreach($nota->items_list as $index => $item)
                                    <div class="item-row p-2.5 bg-white border rounded-3 shadow-sm">
                                        <div class="row g-2 align-items-center">
                                            <div class="col-12 col-md-5">
                                                <label class="form-label small text-muted mb-1" style="font-size: 0.72rem;">Nama Barang / Item</label>
                                                <input type="text" name="items[{{ $index }}][nama]" class="form-control form-control-sm item-nama" value="{{ $item['nama'] ?? '' }}" placeholder="Nama barang..." required>
                                            </div>
                                            <div class="col-4 col-md-2">
                                                <label class="form-label small text-muted mb-1" style="font-size: 0.72rem;">Qty (Jumlah)</label>
                                                <input type="number" name="items[{{ $index }}][qty]" class="form-control form-control-sm item-qty text-center" value="{{ $item['qty'] ?? 1 }}" min="1" step="1" required>
                                            </div>
                                            <div class="col-8 col-md-3">
                                                <label class="form-label small text-muted mb-1" style="font-size: 0.72rem;">Harga Satuan (Rp)</label>
                                                <input type="number" name="items[{{ $index }}][harga]" class="form-control form-control-sm item-harga text-end" value="{{ $item['harga'] ?? 0 }}" min="0" step="100" required>
                                            </div>
                                            <div class="col-10 col-md-2 text-end d-none d-md-block">
                                                <label class="form-label small text-muted mb-1" style="font-size: 0.72rem;">Subtotal</label>
                                                <div class="fw-bold text-dark small item-subtotal-text" style="font-size: 0.85rem; padding-top: 5px;">
                                                    Rp {{ number_format($item['subtotal'] ?? (($item['qty'] ?? 1) * ($item['harga'] ?? 0)), 0, ',', '.') }}
                                                </div>
                                            </div>
                                            <div class="col-2 col-md-auto text-end pt-md-4">
                                                <button type="button" class="btn btn-outline-danger btn-sm py-1 px-2 btn-hapus-item" title="Hapus item ini" style="font-size: 0.75rem;" {{ count($nota->items_list) <= 1 ? 'disabled' : '' }}>
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Total Pembayaran Otomatis --}}
                            <div class="d-flex justify-content-between align-items-center mt-3 pt-2.5 border-top">
                                <div>
                                    <span class="small fw-bold text-dark text-uppercase d-block" style="letter-spacing: 0.05em; font-size: 0.75rem;">
                                        Total Pembayaran:
                                    </span>
                                    <span class="text-muted" id="totalItemCount" style="font-size: 0.72rem;">{{ count($nota->items_list) }} jenis barang</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fs-4 fw-bold text-success" id="displayTotal">
                                        Rp {{ number_format($nota->nominal_pembayaran ?? 0, 0, ',', '.') }}
                                    </span>
                                    <input type="hidden" name="nominal_pembayaran" id="nominal_pembayaran" value="{{ $nota->nominal_pembayaran ?? 0 }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Bukti Pembayaran dengan Kamera Langsung & Upload --}}
                    <div class="col-12">
                        <label class="form-label small fw-semibold text-dark">
                            Lampiran Foto Bukti Pembayaran
                        </label>

                        <div class="p-3.5 border rounded-3 bg-light">
                            {{-- Tampilan Foto yang Sedang Terpasang --}}
                            @if ($nota->bukti_pembayaran)
                                <div class="mb-3 text-center p-2 bg-white rounded border" id="currentPhotoContainer">
                                    <span class="small fw-semibold text-muted d-block mb-1">Bukti Saat Ini:</span>
                                    <img src="{{ asset('storage/' . $nota->bukti_pembayaran) }}"
                                         alt="Bukti Saat Ini"
                                         class="img-fluid rounded border shadow-sm"
                                         style="max-height: 180px;">
                                </div>
                            @endif

                            {{-- Pilihan Tindakan: Ambil Foto Kamera vs Pilih Berkas --}}
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <button type="button" class="btn btn-brand-primary btn-sm py-2 px-3 d-inline-flex align-items-center gap-2" id="btnBukaKamera">
                                    <i class="bi bi-camera-fill"></i>
                                    <span>Foto Ulang via Kamera</span>
                                </button>

                                <label for="bukti_pembayaran" class="btn btn-outline-secondary btn-sm py-2 px-3 d-inline-flex align-items-center gap-2 mb-0" style="cursor: pointer;">
                                    <i class="bi bi-folder2-open"></i>
                                    <span>Ganti File Dokumen</span>
                                </label>
                            </div>

                            <input type="file"
                                   id="bukti_pembayaran"
                                   name="bukti_pembayaran"
                                   accept="image/jpeg,image/png,image/webp"
                                   class="d-none @error('bukti_pembayaran') is-invalid @enderror">

                            <input type="file"
                                   id="kamera_hp"
                                   accept="image/*"
                                   capture="environment"
                                   class="d-none">

                            {{-- Area Kamera Langsung --}}
                            <div id="cameraArea" class="d-none p-3 border rounded-3 bg-dark text-center mb-3">
                                <div class="text-white-50 small mb-2 d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-record-circle text-danger me-1"></i> Kamera Aktif &mdash; Posisikan Struk Baru</span>
                                    <button type="button" class="btn btn-sm btn-outline-light py-0 px-2.5" id="btnTutupKamera">Batal</button>
                                </div>
                                <div class="position-relative d-inline-block w-100" style="max-width: 460px;">
                                    <video id="cameraVideo" autoplay playsinline class="w-100 rounded border shadow" style="max-height: 320px; object-fit: cover; background-color: #000;"></video>
                                    <canvas id="cameraCanvas" class="d-none"></canvas>
                                </div>
                                <div class="mt-3">
                                    <button type="button" class="btn btn-success px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2 shadow" id="btnAmbilFoto">
                                        <i class="bi bi-camera fs-5"></i>
                                        <span>Jepret Foto Baru</span>
                                    </button>
                                </div>
                            </div>

                            {{-- Pratinjau Foto Baru --}}
                            <div id="previewContainer" class="d-none text-center p-3 border rounded-3 bg-white mb-2">
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1">
                                        <i class="bi bi-check-circle me-1"></i> Foto Baru Siap Disimpan
                                    </span>
                                    <button type="button" class="btn btn-link btn-sm text-danger text-decoration-none p-0" id="btnHapusFoto">
                                        <i class="bi bi-trash3 me-1"></i> Batalkan Penggantian
                                    </button>
                                </div>
                                <img id="preview" class="img-fluid rounded border shadow-sm" style="max-height: 240px; object-fit: contain;" alt="Pratinjau Baru">
                                <div class="small text-muted mt-2" id="previewFileName">Foto baru terpasang</div>
                            </div>

                            <div class="small text-muted mt-2">
                                Kosongkan jika tidak ingin mengubah foto. Format: JPG, PNG, WEBP (maks. 10 MB).
                            </div>

                            @error('bukti_pembayaran')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex justify-content-between align-items-center pt-4 mt-4 border-top">
                    <a href="{{ route('nota.index') }}" class="btn btn-outline-secondary px-4">
                        Batal
                    </a>

                    <button type="submit" class="btn btn-brand-primary px-4 py-2 d-inline-flex align-items-center gap-2">
                        <i class="bi bi-check2"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Pengelolaan Dinamis Item Barang & Kalkulasi Otomatis
    let itemIndex = {{ count($nota->items_list) }};
    const itemsContainer = document.getElementById('itemsContainer');
    const btnTambahItem = document.getElementById('btnTambahItem');
    const displayTotal = document.getElementById('displayTotal');
    const nominalPembayaran = document.getElementById('nominal_pembayaran');
    const totalItemCount = document.getElementById('totalItemCount');

    function formatRupiah(angka) {
        return 'Rp ' + (new Intl.NumberFormat('id-ID').format(angka));
    }

    function hitungTotal() {
        if (!itemsContainer) return;
        let total = 0;
        const rows = itemsContainer.querySelectorAll('.item-row');
        
        rows.forEach(row => {
            const qtyInput = row.querySelector('.item-qty');
            const hargaInput = row.querySelector('.item-harga');
            const subtotalText = row.querySelector('.item-subtotal-text');
            
            const qty = parseFloat(qtyInput ? qtyInput.value : 1) || 0;
            const harga = parseFloat(hargaInput ? hargaInput.value : 0) || 0;
            const subtotal = qty * harga;
            
            total += subtotal;
            if (subtotalText) {
                subtotalText.textContent = formatRupiah(subtotal);
            }
        });

        if (displayTotal) displayTotal.textContent = formatRupiah(total);
        if (nominalPembayaran) nominalPembayaran.value = total;

        const count = rows.length;
        if (totalItemCount) totalItemCount.textContent = count + ' jenis barang';

        rows.forEach(row => {
            const btnHapus = row.querySelector('.btn-hapus-item');
            if (btnHapus) {
                btnHapus.disabled = count <= 1;
            }
        });
    }

    if (itemsContainer) {
        itemsContainer.addEventListener('input', function (e) {
            if (e.target.classList.contains('item-qty') || e.target.classList.contains('item-harga')) {
                hitungTotal();
            }
        });

        itemsContainer.addEventListener('click', function (e) {
            const btnHapus = e.target.closest('.btn-hapus-item');
            if (btnHapus && !btnHapus.disabled) {
                const row = btnHapus.closest('.item-row');
                if (row && itemsContainer.querySelectorAll('.item-row').length > 1) {
                    row.remove();
                    hitungTotal();
                }
            }
        });
    }

    if (btnTambahItem) {
        btnTambahItem.addEventListener('click', function () {
            const newRow = document.createElement('div');
            newRow.className = 'item-row p-2.5 bg-white border rounded-3 shadow-sm';
            newRow.innerHTML = `
                <div class="row g-2 align-items-center">
                    <div class="col-12 col-md-5">
                        <label class="form-label small text-muted mb-1" style="font-size: 0.72rem;">Nama Barang / Item</label>
                        <input type="text" name="items[${itemIndex}][nama]" class="form-control form-control-sm item-nama" placeholder="Nama barang..." required>
                    </div>
                    <div class="col-4 col-md-2">
                        <label class="form-label small text-muted mb-1" style="font-size: 0.72rem;">Qty (Jumlah)</label>
                        <input type="number" name="items[${itemIndex}][qty]" class="form-control form-control-sm item-qty text-center" value="1" min="1" step="1" required>
                    </div>
                    <div class="col-8 col-md-3">
                        <label class="form-label small text-muted mb-1" style="font-size: 0.72rem;">Harga Satuan (Rp)</label>
                        <input type="number" name="items[${itemIndex}][harga]" class="form-control form-control-sm item-harga text-end" placeholder="0" min="0" step="100" required>
                    </div>
                    <div class="col-10 col-md-2 text-end d-none d-md-block">
                        <label class="form-label small text-muted mb-1" style="font-size: 0.72rem;">Subtotal</label>
                        <div class="fw-bold text-dark small item-subtotal-text" style="font-size: 0.85rem; padding-top: 5px;">Rp 0</div>
                    </div>
                    <div class="col-2 col-md-auto text-end pt-md-4">
                        <button type="button" class="btn btn-outline-danger btn-sm py-1 px-2 btn-hapus-item" title="Hapus item ini" style="font-size: 0.75rem;">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                </div>
            `;
            itemsContainer.appendChild(newRow);
            itemIndex++;
            hitungTotal();

            const inputBaru = newRow.querySelector('.item-nama');
            if (inputBaru) inputBaru.focus();
        });
    }

    hitungTotal();

    let mediaStream = null;

    const fileInput = document.getElementById('bukti_pembayaran');
    const kameraHpInput = document.getElementById('kamera_hp');
    const btnBukaKamera = document.getElementById('btnBukaKamera');
    const btnTutupKamera = document.getElementById('btnTutupKamera');
    const btnAmbilFoto = document.getElementById('btnAmbilFoto');
    const btnHapusFoto = document.getElementById('btnHapusFoto');
    const cameraArea = document.getElementById('cameraArea');
    const cameraVideo = document.getElementById('cameraVideo');
    const cameraCanvas = document.getElementById('cameraCanvas');
    const previewContainer = document.getElementById('previewContainer');
    const previewImg = document.getElementById('preview');
    const previewFileName = document.getElementById('previewFileName');
    const currentPhotoContainer = document.getElementById('currentPhotoContainer');

    btnBukaKamera.addEventListener('click', async function () {
        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        
        if (isMobile) {
            kameraHpInput.click();
            return;
        }

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            if (!window.isSecureContext && window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1') {
                alert("Perhatian Browser:\nKamera di laptop memerlukan protokol aman (HTTPS atau localhost) oleh aturan keamanan Chrome/Edge.\n\nCara Membuka Kamera di Laptop:\n1. Buka via HTTPS di: https://nota-kontan.test/nota-kontan/" + {{ $nota->id }} + "/edit\n(Aktifkan SSL di Laragon: Klik kanan Laragon -> Apache -> SSL -> centang Enabled)\n\n2. Atau buka via alamat: http://127.0.0.1:8000/nota-kontan/" + {{ $nota->id }} + "/edit\n\n*Catatan: Saat di-hosting di Railway nanti, HTTPS sudah otomatis aktif 100%.");
            } else {
                alert("Webcam tidak terdeteksi atau diblokir di laptop Anda.");
            }
            kameraHpInput.click();
            return;
        }

        try {
            mediaStream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'environment', width: { ideal: 1280 }, height: { ideal: 720 } }
            });
            cameraVideo.srcObject = mediaStream;
            cameraArea.classList.remove('d-none');
            previewContainer.classList.add('d-none');
        } catch (err) {
            console.warn('Gagal akses kamera langsung:', err);
            if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
                alert("Akses kamera ditolak oleh browser. Silakan klik ikon gembok/kamera di bilah URL dan pilih 'Izinkan'.");
            } else if (err.name === 'NotFoundError' || err.name === 'DevicesNotFoundError') {
                alert("Webcam tidak ditemukan atau sedang dipakai aplikasi lain (seperti Zoom/Meet).");
            } else {
                alert("Kendala membuka kamera: " + err.message);
            }
            kameraHpInput.click();
        }
    });

    btnTutupKamera.addEventListener('click', function () {
        hentikanStream();
        cameraArea.classList.add('d-none');
    });

    btnAmbilFoto.addEventListener('click', function () {
        if (!mediaStream) return;

        cameraCanvas.width = cameraVideo.videoWidth || 640;
        cameraCanvas.height = cameraVideo.videoHeight || 480;
        const ctx = cameraCanvas.getContext('2d');
        ctx.drawImage(cameraVideo, 0, 0, cameraCanvas.width, cameraCanvas.height);

        cameraCanvas.toBlob(function (blob) {
            const fileName = 'kamera_struk_' + Date.now() + '.jpg';
            const file = new File([blob], fileName, { type: 'image/jpeg' });

            const dt = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;

            previewImg.src = URL.createObjectURL(blob);
            previewFileName.textContent = fileName + ' (Diambil dari kamera laptop)';
            previewContainer.classList.remove('d-none');

            hentikanStream();
            cameraArea.classList.add('d-none');
        }, 'image/jpeg', 0.92);
    });

    kameraHpInput.addEventListener('change', function (e) {
        if (e.target.files && e.target.files[0]) {
            const file = e.target.files[0];
            const dt = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;

            previewImg.src = URL.createObjectURL(file);
            previewFileName.textContent = file.name + ' (Kamera HP)';
            previewContainer.classList.remove('d-none');
            hentikanStream();
            cameraArea.classList.add('d-none');
        }
    });

    fileInput.addEventListener('change', function (e) {
        if (e.target.files && e.target.files[0]) {
            const file = e.target.files[0];
            previewImg.src = URL.createObjectURL(file);
            previewFileName.textContent = file.name;
            previewContainer.classList.remove('d-none');
            hentikanStream();
            cameraArea.classList.add('d-none');
        }
    });

    btnHapusFoto.addEventListener('click', function () {
        fileInput.value = '';
        kameraHpInput.value = '';
        previewImg.removeAttribute('src');
        previewContainer.classList.add('d-none');
    });

    function hentikanStream() {
        if (mediaStream) {
            mediaStream.getTracks().forEach(track => track.stop());
            mediaStream = null;
        }
    }
</script>
@endpush
