@extends('layouts.app')

@section('title', 'Input Konfirmasi DO Baru')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-9">
        {{-- Breadcrumb --}}
        <div class="d-flex align-items-center gap-2 mb-3">
            <a href="{{ route('nota.index') }}" class="btn btn-outline-secondary btn-sm px-2.5 py-1.5" title="Kembali ke Daftar">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="h5 fw-bold text-dark mb-0">Input Konfirmasi DO & Pembelian Mobil</h1>
                <span class="text-muted small">ASTRA DAIHATSU Palembang Veteran &bull; Pencatatan Transaksi & Pengarsipan Bukti Bayar</span>
            </div>
        </div>

        <div class="card card-custom p-4 p-md-5 shadow-sm">
            <form action="{{ route('nota.store') }}" method="POST" enctype="multipart/form-data" id="formNota">
                @csrf

                {{-- SECTION 1: DATA PELANGGAN --}}
                <div class="mb-4">
                    <div class="d-flex align-items-center gap-2 pb-2 mb-3 border-bottom">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-circle d-flex align-items-center justify-content-center" style="width: 26px; height: 26px; font-size: 0.8rem;">1</span>
                        <h6 class="fw-bold text-dark mb-0 text-uppercase" style="font-size: 0.82rem; letter-spacing: 0.05em;">Data Customer</h6>
                    </div>

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
                                   placeholder="Contoh: Irene Lemansyah"
                                   required
                                   maxlength="150">
                            @error('nama_customer')
                                <div class="invalid-feedback small">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Nomor WhatsApp Customer --}}
                        <div class="col-md-6">
                            <label for="nomor_wa" class="form-label small fw-semibold text-dark">
                                No HP / WhatsApp Customer <span class="text-danger">*</span>
                            </label>
                            <input type="tel"
                                   id="nomor_wa"
                                   name="nomor_wa"
                                   class="form-control @error('nomor_wa') is-invalid @enderror"
                                   value="{{ old('nomor_wa') }}"
                                   placeholder="Contoh: 085709683797"
                                   required
                                   maxlength="25">
                            <small class="text-muted" style="font-size: 0.72rem;">Digunakan untuk mengirim pesan konfirmasi DO otomatis.</small>
                            @error('nomor_wa')
                                <div class="invalid-feedback small">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Nama STNK / BPKB --}}
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="nama_stnk_bpkb" class="form-label small fw-semibold text-dark mb-0">
                                    Nama STNK / BPKB <span class="text-danger">*</span>
                                </label>
                                <div class="form-check form-check-inline m-0">
                                    <input class="form-check-input" type="checkbox" id="checkSamakanStnk">
                                    <label class="form-check-label small text-muted" for="checkSamakanStnk" style="font-size: 0.75rem;">
                                        Samakan dengan Nama Customer
                                    </label>
                                </div>
                            </div>
                            <input type="text"
                                   id="nama_stnk_bpkb"
                                   name="nama_stnk_bpkb"
                                   class="form-control @error('nama_stnk_bpkb') is-invalid @enderror"
                                   value="{{ old('nama_stnk_bpkb') }}"
                                   placeholder="Nama yang tercetak pada berkas STNK / BPKB"
                                   maxlength="150">
                            @error('nama_stnk_bpkb')
                                <div class="invalid-feedback small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- SECTION 2: DATA UNIT KENDARAAN --}}
                <div class="mb-4">
                    <div class="d-flex align-items-center gap-2 pb-2 mb-3 border-bottom">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-circle d-flex align-items-center justify-content-center" style="width: 26px; height: 26px; font-size: 0.8rem;">2</span>
                        <h6 class="fw-bold text-dark mb-0 text-uppercase" style="font-size: 0.82rem; letter-spacing: 0.05em;">Spesifikasi Unit Mobil</h6>
                    </div>

                    <div class="row g-3">
                        {{-- Type Unit --}}
                        <div class="col-md-6">
                            <label for="type_unit" class="form-label small fw-semibold text-dark">
                                Type Unit <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   id="type_unit"
                                   name="type_unit"
                                   list="listTypeUnit"
                                   class="form-control @error('type_unit') is-invalid @enderror"
                                   value="{{ old('type_unit') }}"
                                   placeholder="Pilih atau ketik, cth: Granmax PU 1.3 STD"
                                   required>
                            <datalist id="listTypeUnit">
                                <option value="Granmax PU 1.3 STD">
                                <option value="Granmax PU 1.5 STD">
                                <option value="Granmax PU 1.5 AC PS">
                                <option value="Granmax Minibus 1.3 D">
                                <option value="Granmax Blind Van 1.3">
                                <option value="Sigra M 1.0 MT MC">
                                <option value="Sigra D 1.0 MT">
                                <option value="Sigra X 1.2 MT">
                                <option value="Sigra X 1.2 AT">
                                <option value="Sigra R 1.2 MT">
                                <option value="Sigra R 1.2 AT">
                                <option value="All New Ayla 1.0 M MT">
                                <option value="All New Ayla 1.0 X CVT">
                                <option value="All New Ayla 1.2 R CVT">
                                <option value="Terios X MT">
                                <option value="Terios X AT">
                                <option value="Terios R MT">
                                <option value="Terios R AT">
                                <option value="Terios Custom">
                                <option value="Rocky 1.2 M MT">
                                <option value="Rocky 1.2 X CVT">
                                <option value="Rocky 1.0 R TC CVT">
                                <option value="All New Xenia 1.3 M MT">
                                <option value="All New Xenia 1.3 X MT">
                                <option value="All New Xenia 1.3 R CVT">
                                <option value="All New Xenia 1.5 R CVT">
                                <option value="Luxio 1.5 D">
                                <option value="Luxio 1.5 X">
                            </datalist>
                            @error('type_unit')
                                <div class="invalid-feedback small">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Warna --}}
                        <div class="col-md-3">
                            <label for="warna" class="form-label small fw-semibold text-dark">
                                Warna <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   id="warna"
                                   name="warna"
                                   list="listWarna"
                                   class="form-control @error('warna') is-invalid @enderror"
                                   value="{{ old('warna') }}"
                                   placeholder="Cth: Hitam, Grey, Putih"
                                   required>
                            <datalist id="listWarna">
                                <option value="Hitam">
                                <option value="Grey">
                                <option value="Putih">
                                <option value="Silver">
                                <option value="Merah">
                                <option value="Orange">
                                <option value="Bronze">
                            </datalist>
                            @error('warna')
                                <div class="invalid-feedback small">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tahun Mobil --}}
                        <div class="col-md-3">
                            <label for="tahun_mobil" class="form-label small fw-semibold text-dark">
                                Tahun Mobil <span class="text-danger">*</span>
                            </label>
                            <select id="tahun_mobil" name="tahun_mobil" class="form-select @error('tahun_mobil') is-invalid @enderror" required>
                                @php $currentYear = (int) date('Y'); @endphp
                                @for($y = $currentYear + 1; $y >= $currentYear - 3; $y--)
                                    <option value="{{ $y }}" {{ old('tahun_mobil', $currentYear) == $y ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endfor
                            </select>
                            @error('tahun_mobil')
                                <div class="invalid-feedback small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- SECTION 3: SKEMA PEMBELIAN & FINANSIAL --}}
                <div class="mb-4">
                    <div class="d-flex align-items-center gap-2 pb-2 mb-3 border-bottom">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-circle d-flex align-items-center justify-content-center" style="width: 26px; height: 26px; font-size: 0.8rem;">3</span>
                        <h6 class="fw-bold text-dark mb-0 text-uppercase" style="font-size: 0.82rem; letter-spacing: 0.05em;">Skema Pembelian & Pembayaran</h6>
                    </div>

                    <div class="row g-3">
                        {{-- Cara Pembelian (Credit / Cash) --}}
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-dark d-block">
                                Cara Pembelian <span class="text-danger">*</span>
                            </label>
                            <div class="d-flex gap-3 pt-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="cara_pembelian" id="caraCredit" value="Credit" {{ old('cara_pembelian', 'Credit') === 'Credit' ? 'checked' : '' }} onchange="toggleLeasingFields()">
                                    <label class="form-check-label fw-semibold text-dark small" for="caraCredit">
                                        Credit
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="cara_pembelian" id="caraCash" value="Cash" {{ old('cara_pembelian') === 'Cash' ? 'checked' : '' }} onchange="toggleLeasingFields()">
                                    <label class="form-check-label fw-semibold text-dark small" for="caraCash">
                                        Cash (Tunai)
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Nama Leasing --}}
                        <div class="col-md-4" id="colLeasing">
                            <label for="nama_leasing" class="form-label small fw-semibold text-dark">
                                Nama Leasing <span class="text-danger" id="reqLeasing">*</span>
                            </label>
                            <input type="text"
                                   id="nama_leasing"
                                   name="nama_leasing"
                                   list="listLeasing"
                                   class="form-control @error('nama_leasing') is-invalid @enderror"
                                   value="{{ old('nama_leasing') }}"
                                   placeholder="Pilih atau ketik, cth: TAFS">
                            <datalist id="listLeasing">
                                <option value="TAFS">
                                <option value="ACC">
                                <option value="Adira Finance">
                                <option value="BCA Finance">
                                <option value="Mandiri Utama Finance (MUF)">
                                <option value="OTO Multiartha">
                                <option value="Maybank Finance">
                                <option value="CIMB Niaga Auto Finance">
                            </datalist>
                            @error('nama_leasing')
                                <div class="invalid-feedback small">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tenor --}}
                        <div class="col-md-4" id="colTenor">
                            <label for="tenor" class="form-label small fw-semibold text-dark">
                                Tenor <span class="text-danger" id="reqTenor">*</span>
                            </label>
                            <select id="tenor" name="tenor" class="form-select @error('tenor') is-invalid @enderror">
                                <option value="">-- Pilih Tenor --</option>
                                <option value="1 Tahun" {{ old('tenor') === '1 Tahun' ? 'selected' : '' }}>1 Tahun (12 Bulan)</option>
                                <option value="2 Tahun" {{ old('tenor') === '2 Tahun' ? 'selected' : '' }}>2 Tahun (24 Bulan)</option>
                                <option value="3 Tahun" {{ old('tenor') === '3 Tahun' ? 'selected' : '' }}>3 Tahun (36 Bulan)</option>
                                <option value="4 Tahun" {{ old('tenor') === '4 Tahun' ? 'selected' : '' }}>4 Tahun (48 Bulan)</option>
                                <option value="5 Tahun" {{ old('tenor', '5 Tahun') === '5 Tahun' ? 'selected' : '' }}>5 Tahun (60 Bulan)</option>
                                <option value="6 Tahun" {{ old('tenor') === '6 Tahun' ? 'selected' : '' }}>6 Tahun (72 Bulan)</option>
                            </select>
                            @error('tenor')
                                <div class="invalid-feedback small">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Total Bayar (Nominal Pembayaran) --}}
                        <div class="col-md-4">
                            <label for="display_total_bayar" class="form-label small fw-semibold text-dark">
                                Total Bayar (Rp) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-dark fw-bold small">Rp</span>
                                <input type="text"
                                       id="display_total_bayar"
                                       class="form-control fw-bold text-success fs-6"
                                       placeholder="Cth: 81.500.000"
                                       required>
                            </div>
                            <input type="hidden" name="nominal_pembayaran" id="nominal_pembayaran" value="{{ old('nominal_pembayaran', 0) }}">
                            <small class="text-muted" style="font-size: 0.72rem;">Uang muka (DP) atau pelunasan total.</small>
                            @error('nominal_pembayaran')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Total Diskon --}}
                        <div class="col-md-4">
                            <label for="display_total_diskon" class="form-label small fw-semibold text-dark">
                                Total Diskon (Rp)
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-dark fw-bold small">Rp</span>
                                <input type="text"
                                       id="display_total_diskon"
                                       class="form-control fw-semibold"
                                       placeholder="Cth: 42.000.000">
                            </div>
                            <input type="hidden" name="total_diskon" id="total_diskon" value="{{ old('total_diskon', 0) }}">
                            <small class="text-muted" style="font-size: 0.72rem;">Potongan diskon / promo unit.</small>
                            @error('total_diskon')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tanggal Transaksi --}}
                        <div class="col-md-4">
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
                    </div>
                </div>

                {{-- SECTION 4: FOTO BUKTI TRANSFER / STRUK --}}
                <div class="mb-4">
                    <div class="d-flex align-items-center gap-2 pb-2 mb-3 border-bottom">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-circle d-flex align-items-center justify-content-center" style="width: 26px; height: 26px; font-size: 0.8rem;">4</span>
                        <h6 class="fw-bold text-dark mb-0 text-uppercase" style="font-size: 0.82rem; letter-spacing: 0.05em;">Foto Bukti Transfer / Struk / Tanda Jadi</h6>
                    </div>

                    <div class="p-3.5 border rounded-3 bg-light">
                        {{-- Tombol Kamera & Berkas --}}
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <button type="button" class="btn btn-brand-primary btn-sm py-2 px-3 d-inline-flex align-items-center gap-2" id="btnBukaKamera">
                                <i class="bi bi-camera-fill"></i>
                                <span>Ambil Foto Langsung (Kamera)</span>
                            </button>

                            <label for="bukti_pembayaran" class="btn btn-outline-secondary btn-sm py-2 px-3 d-inline-flex align-items-center gap-2 mb-0 cursor-pointer">
                                <i class="bi bi-folder2-open"></i>
                                <span>Pilih Berkas Dokumen</span>
                            </label>

                            <input type="file"
                                   id="bukti_pembayaran"
                                   name="bukti_pembayaran"
                                   class="d-none"
                                   accept="image/*">

                            <input type="file"
                                   id="kamera_hp"
                                   class="d-none"
                                   accept="image/*"
                                   capture="environment">
                        </div>

                        {{-- Area Kamera Webcam PC/Laptop --}}
                        <div id="cameraArea" class="d-none mb-3">
                            <div class="position-relative bg-dark rounded overflow-hidden text-center" style="max-width: 480px; margin: 0 auto;">
                                <video id="cameraVideo" autoplay playsinline class="w-100" style="max-height: 320px; object-fit: cover;"></video>
                                <canvas id="cameraCanvas" class="d-none"></canvas>

                                <div class="position-absolute bottom-0 start-0 end-0 p-3 d-flex justify-content-center gap-2" style="background: linear-gradient(transparent, rgba(0,0,0,0.7));">
                                    <button type="button" class="btn btn-light btn-sm fw-semibold d-inline-flex align-items-center gap-1.5" id="btnAmbilFoto">
                                        <i class="bi bi-camera"></i>
                                        <span>Jepret Foto</span>
                                    </button>
                                    <button type="button" class="btn btn-outline-light btn-sm" id="btnTutupKamera">
                                        Batal
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Pratinjau Foto Terpilih --}}
                        <div id="previewContainer" class="d-none text-center p-3 bg-white border rounded">
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                <span class="small fw-bold text-success d-inline-flex align-items-center gap-1">
                                    <i class="bi bi-check-circle-fill"></i> Foto Bukti Siap Diunggah
                                </span>
                                <button type="button" class="btn btn-link btn-sm text-danger text-decoration-none p-0" id="btnHapusFoto">
                                    <i class="bi bi-trash3 me-1"></i> Hapus / Foto Ulang
                                </button>
                            </div>
                            <img id="preview" class="img-fluid rounded border shadow-sm" style="max-height: 280px; object-fit: contain;" alt="Pratinjau Struk">
                            <div class="small text-muted mt-2" id="previewFileName">Bukti struk terlampir</div>
                        </div>

                        <div class="small text-muted mt-2" id="formatInfo">
                            Format: JPG, PNG, WEBP &bull; Ukuran maksimal: 20 MB &bull; Bisa foto langsung via kamera HP/Laptop
                        </div>

                        @error('bukti_pembayaran')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex justify-content-between align-items-center pt-4 mt-4 border-top">
                    <a href="{{ route('nota.index') }}" class="btn btn-outline-secondary px-4">
                        Batal
                    </a>

                    <button type="submit" class="btn btn-brand-primary px-4 py-2 d-inline-flex align-items-center gap-2">
                        <i class="bi bi-check2"></i>
                        <span>Simpan Data Konfirmasi DO</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Samakan Nama STNK/BPKB dengan Nama Customer
    const namaCustomerInput = document.getElementById('nama_customer');
    const namaStnkInput = document.getElementById('nama_stnk_bpkb');
    const checkSamakanStnk = document.getElementById('checkSamakanStnk');

    if (checkSamakanStnk) {
        checkSamakanStnk.addEventListener('change', function () {
            if (this.checked) {
                namaStnkInput.value = namaCustomerInput.value;
            }
        });

        namaCustomerInput.addEventListener('input', function () {
            if (checkSamakanStnk.checked) {
                namaStnkInput.value = this.value;
            }
        });
    }

    // Toggle Leasing & Tenor jika Cash vs Credit
    function toggleLeasingFields() {
        const isCredit = document.getElementById('caraCredit').checked;
        const colLeasing = document.getElementById('colLeasing');
        const colTenor = document.getElementById('colTenor');
        const inputLeasing = document.getElementById('nama_leasing');
        const selectTenor = document.getElementById('tenor');
        const reqLeasing = document.getElementById('reqLeasing');
        const reqTenor = document.getElementById('reqTenor');

        if (isCredit) {
            colLeasing.style.opacity = '1';
            colTenor.style.opacity = '1';
            inputLeasing.disabled = false;
            selectTenor.disabled = false;
            reqLeasing.style.display = 'inline';
            reqTenor.style.display = 'inline';
            if (inputLeasing.value === '-') inputLeasing.value = '';
        } else {
            colLeasing.style.opacity = '0.5';
            colTenor.style.opacity = '0.5';
            inputLeasing.disabled = true;
            selectTenor.disabled = true;
            inputLeasing.value = '-';
            selectTenor.value = '-';
            reqLeasing.style.display = 'none';
            reqTenor.style.display = 'none';
        }
    }

    // Pemformatan Angka Ribuan Rupiah Otomatis
    function setupRupiahInput(displayId, hiddenId) {
        const display = document.getElementById(displayId);
        const hidden = document.getElementById(hiddenId);

        if (!display || !hidden) return;

        // Inisialisasi awal
        if (hidden.value && hidden.value > 0) {
            display.value = new Intl.NumberFormat('id-ID').format(hidden.value);
        }

        display.addEventListener('input', function () {
            let val = this.value.replace(/\D/g, '');
            if (val === '') {
                hidden.value = 0;
                this.value = '';
            } else {
                hidden.value = parseInt(val, 10);
                this.value = new Intl.NumberFormat('id-ID').format(val);
            }
        });
    }

    setupRupiahInput('display_total_bayar', 'nominal_pembayaran');
    setupRupiahInput('display_total_diskon', 'total_diskon');

    // Inisialisasi state awal
    toggleLeasingFields();

    // Kamera & Upload File Bukti Bayar
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

    // Buka Kamera
    btnBukaKamera.addEventListener('click', async function () {
        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        
        // Di smartphone HP, buka langsung aplikasi kamera HP
        if (isMobile) {
            kameraHpInput.click();
            return;
        }

        // Di laptop / PC
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            alert("Webcam tidak terdeteksi atau diblokir di laptop Anda.");
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
            kameraHpInput.click();
        }
    });

    // Tutup Kamera
    btnTutupKamera.addEventListener('click', function () {
        hentikanStream();
        cameraArea.classList.add('d-none');
    });

    // Jepret Foto dari Webcam Laptop
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

    // Handler Foto dari Kamera HP
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

    // Handler Pilih Berkas
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

    // Hapus Foto
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
