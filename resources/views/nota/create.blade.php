@extends('layouts.app')

@section('title', 'Input Nota Baru')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-8">
        {{-- Breadcrumb --}}
        <div class="d-flex align-items-center gap-2 mb-3">
            <a href="{{ route('nota.index') }}" class="btn btn-outline-secondary btn-sm px-2.5 py-1.5" title="Kembali ke Daftar">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="h5 fw-bold text-dark mb-0">Input Nota Kontan Baru</h1>
                <span class="text-muted small">Pencatatan transaksi kas masuk dan pengarsipan struk</span>
            </div>
        </div>

        <div class="card card-custom p-4 p-md-5 shadow-sm">
            <form action="{{ route('nota.store') }}" method="POST" enctype="multipart/form-data" id="formNota">
                @csrf

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
                               value="{{ old('nama_customer') }}"
                               placeholder="Nama lengkap pembeli / perusahaan"
                               required
                               maxlength="150">
                        @error('nama_customer')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Nomor WhatsApp --}}
                    <div class="col-md-6">
                        <label for="nomor_wa" class="form-label small fw-semibold text-dark">
                            Nomor WhatsApp Customer <span class="text-danger">*</span>
                        </label>
                        <input type="tel"
                               id="nomor_wa"
                               name="nomor_wa"
                               class="form-control @error('nomor_wa') is-invalid @enderror"
                               value="{{ old('nomor_wa') }}"
                               placeholder="Contoh: 081234567890"
                               required
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
                               value="{{ old('tanggal_upload', now()->format('Y-m-d')) }}"
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
                                    Bisa memasukkan lebih dari 1 barang / item beserta harga satuannya
                                </span>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm py-1.5 px-3 d-inline-flex align-items-center gap-1.5" id="btnTambahItem" style="font-size: 0.78rem;">
                                <i class="bi bi-plus-circle"></i>
                                <span>Tambah Item Baru</span>
                            </button>
                        </div>

                        <div class="card bg-light border p-2.5 p-md-3 rounded-3 shadow-none">
                            <div id="itemsContainer" class="d-flex flex-column gap-2">
                                {{-- Baris item pertama --}}
                                <div class="item-row p-2.5 bg-white border rounded-3 shadow-sm">
                                    <div class="row g-2 align-items-center">
                                        <div class="col-12 col-md-5">
                                            <label class="form-label small text-muted mb-1" style="font-size: 0.72rem;">Nama Barang / Item</label>
                                            <input type="text" name="items[0][nama]" class="form-control form-control-sm item-nama" placeholder="Contoh: Oli Mesin, Baut, Servis..." required>
                                        </div>
                                        <div class="col-4 col-md-2">
                                            <label class="form-label small text-muted mb-1" style="font-size: 0.72rem;">Qty (Jumlah)</label>
                                            <input type="number" name="items[0][qty]" class="form-control form-control-sm item-qty text-center" value="1" min="1" step="1" required>
                                        </div>
                                        <div class="col-8 col-md-3">
                                            <label class="form-label small text-muted mb-1" style="font-size: 0.72rem;">Harga Satuan (Rp)</label>
                                            <input type="number" name="items[0][harga]" class="form-control form-control-sm item-harga text-end" placeholder="0" min="0" step="100" required>
                                        </div>
                                        <div class="col-10 col-md-2 text-end d-none d-md-block">
                                            <label class="form-label small text-muted mb-1" style="font-size: 0.72rem;">Subtotal</label>
                                            <div class="fw-bold text-dark small item-subtotal-text" style="font-size: 0.85rem; padding-top: 5px;">Rp 0</div>
                                        </div>
                                        <div class="col-2 col-md-auto text-end pt-md-4">
                                            <button type="button" class="btn btn-outline-danger btn-sm py-1 px-2 btn-hapus-item" title="Hapus item ini" style="font-size: 0.75rem;" disabled>
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Total Pembayaran Otomatis --}}
                            <div class="d-flex justify-content-between align-items-center mt-3 pt-2.5 border-top">
                                <div>
                                    <span class="small fw-bold text-dark text-uppercase d-block" style="letter-spacing: 0.05em; font-size: 0.75rem;">
                                        Total Pembayaran:
                                    </span>
                                    <span class="text-muted" id="totalItemCount" style="font-size: 0.72rem;">1 jenis barang</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fs-4 fw-bold text-success" id="displayTotal">Rp 0</span>
                                    <input type="hidden" name="nominal_pembayaran" id="nominal_pembayaran" value="0">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Bukti Pembayaran (Kamera Langsung & Upload Dokumen) --}}
                    <div class="col-12">
                        <label class="form-label small fw-semibold text-dark">
                            Foto Bukti Transfer / Struk <span class="text-danger">*</span>
                        </label>

                        <div class="p-3.5 border rounded-3 bg-light">
                            {{-- Pilihan Tindakan: Ambil Foto Kamera vs Pilih Berkas --}}
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                {{-- Tombol Buka Kamera --}}
                                <button type="button" class="btn btn-brand-primary btn-sm py-2 px-3 d-inline-flex align-items-center gap-2" id="btnBukaKamera">
                                    <i class="bi bi-camera-fill"></i>
                                    <span>Ambil Foto Langsung (Kamera)</span>
                                </button>

                                {{-- Tombol Pilih Berkas dari Perangkat --}}
                                <label for="bukti_pembayaran" class="btn btn-outline-secondary btn-sm py-2 px-3 d-inline-flex align-items-center gap-2 mb-0" style="cursor: pointer;">
                                    <i class="bi bi-folder2-open"></i>
                                    <span>Pilih Berkas Dokumen</span>
                                </label>
                            </div>

                            {{-- Input file tersembunyi yang akan dikirimkan ke server --}}
                            <input type="file"
                                   id="bukti_pembayaran"
                                   name="bukti_pembayaran"
                                   accept="image/jpeg,image/png,image/webp"
                                   class="d-none @error('bukti_pembayaran') is-invalid @enderror"
                                   required>

                            {{-- Input kamera langsung untuk HP (capture=environment) --}}
                            <input type="file"
                                   id="kamera_hp"
                                   accept="image/*"
                                   capture="environment"
                                   class="d-none">

                            {{-- Viewfinder Kamera Interaktif (Laptop Webcam / Browser View) --}}
                            <div id="cameraArea" class="d-none p-3 border rounded-3 bg-dark text-center mb-3">
                                <div class="text-white-50 small mb-2 d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-record-circle text-danger me-1"></i> Kamera Aktif &mdash; Posisikan Struk di Depan Kamera</span>
                                    <button type="button" class="btn btn-sm btn-outline-light py-0 px-2.5" id="btnTutupKamera">Batal</button>
                                </div>
                                <div class="position-relative d-inline-block w-100" style="max-width: 460px;">
                                    <video id="cameraVideo" autoplay playsinline class="w-100 rounded border shadow" style="max-height: 320px; object-fit: cover; background-color: #000;"></video>
                                    <canvas id="cameraCanvas" class="d-none"></canvas>
                                </div>
                                <div class="mt-3">
                                    <button type="button" class="btn btn-success px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2 shadow" id="btnAmbilFoto">
                                        <i class="bi bi-camera fs-5"></i>
                                        <span>Jepret / Ambil Foto</span>
                                    </button>
                                </div>
                            </div>

                            {{-- Pratinjau Foto yang Berhasil Diambil / Dipilih --}}
                            <div id="previewContainer" class="d-none text-center p-3 border rounded-3 bg-white">
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1">
                                        <i class="bi bi-check-circle me-1"></i> Foto Siap Digunakan
                                    </span>
                                    <button type="button" class="btn btn-link btn-sm text-danger text-decoration-none p-0" id="btnHapusFoto">
                                        <i class="bi bi-trash3 me-1"></i> Hapus / Foto Ulang
                                    </button>
                                </div>
                                <img id="preview" class="img-fluid rounded border shadow-sm" style="max-height: 280px; object-fit: contain;" alt="Pratinjau Struk">
                                <div class="small text-muted mt-2" id="previewFileName">Bukti struk terlampir</div>
                            </div>

                            <div class="small text-muted mt-2" id="formatInfo">
                                Format: JPG, PNG, WEBP &bull; Ukuran maksimal: 10 MB &bull; Bisa foto langsung via kamera HP/Laptop
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
                        <span>Simpan Data Transaksi</span>
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
    let itemIndex = 1;
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

        // Update status disabled pada tombol hapus
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
                        <input type="text" name="items[${itemIndex}][nama]" class="form-control form-control-sm item-nama" placeholder="Contoh: Oli Mesin, Baut, Servis..." required>
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
    const formatInfo = document.getElementById('formatInfo');

    // Buka Kamera (Deteksi apakah HP atau Laptop Webcam)
    btnBukaKamera.addEventListener('click', async function () {
        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        
        // Di smartphone HP, buka langsung aplikasi kamera HP asli
        if (isMobile) {
            kameraHpInput.click();
            return;
        }

        // Di laptop / PC, periksa ketersediaan API kamera browser (getUserMedia)
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            if (!window.isSecureContext && window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1') {
                alert("Perhatian Browser:\nKamera di laptop memerlukan protokol aman (HTTPS atau localhost) oleh aturan keamanan Chrome/Edge.\n\nCara Membuka Kamera di Laptop:\n1. Buka via HTTPS di: https://nota-kontan.test/nota-kontan/upload\n(Aktifkan SSL di Laragon: Klik kanan Laragon -> Apache -> SSL -> centang Enabled)\n\n2. Atau buka via alamat: http://127.0.0.1:8000/nota-kontan/upload\n\n*Catatan: Saat di-hosting di Railway nanti, HTTPS sudah otomatis aktif 100%.");
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
            console.warn('Gagal mengakses kamera langsung:', err);
            if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
                alert("Akses kamera ditolak oleh browser. Silakan klik ikon gembok/kamera di sebelah kiri bilah alamat URL browser Anda dan pilih 'Izinkan' (Allow) untuk kamera.");
            } else if (err.name === 'NotFoundError' || err.name === 'DevicesNotFoundError') {
                alert("Webcam tidak ditemukan atau sedang digunakan oleh aplikasi lain (seperti Zoom/Meet).");
            } else {
                alert("Kendala membuka kamera: " + err.message);
            }
            kameraHpInput.click();
        }
    });

    // Tutup Kamera Webcam
    btnTutupKamera.addEventListener('click', function () {
        hentikanStream();
        cameraArea.classList.add('d-none');
    });

    // Jepret Foto dari Video Webcam
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

    // Handler ketika foto diambil dari Kamera HP
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

    // Handler ketika memilih berkas manual
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

    // Hapus / Ganti Foto
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
