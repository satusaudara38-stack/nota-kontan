{{-- Modal Kirim Nota ke WhatsApp Bos --}}
<div class="modal fade" id="modalKirimBos{{ $nota->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $nota->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content card-custom shadow-lg border-0">
            {{-- Modal Header --}}
            <div class="modal-header border-bottom py-3 px-4" style="background-color: #0f172a; color: #ffffff;">
                <div>
                    <h5 class="modal-title fs-6 fw-bold mb-0" id="modalLabel{{ $nota->id }}">
                        Pengiriman Laporan Nota #{{ str_pad($nota->id, 5, '0', STR_PAD_LEFT) }}
                    </h5>
                    <div class="small text-white-50" style="font-size: 0.75rem;">
                        Distribusi dokumen digital kepada jajaran pimpinan
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-3 p-md-4" style="-webkit-overflow-scrolling: touch; overflow-y: auto;">
                {{-- Ringkasan Dokumen --}}
                <div class="p-3 rounded-3 mb-3" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                        <span class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem; letter-spacing: 0.05em;">Ringkasan Nota</span>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-semibold">
                            Rp {{ number_format($nota->nominal_pembayaran, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="row g-1 small">
                        <div class="col-4 text-muted">Customer</div>
                        <div class="col-8 fw-semibold text-dark">{{ $nota->nama_customer }}</div>

                        <div class="col-4 text-muted">Barang/Jasa</div>
                        <div class="col-8 text-dark">{{ $nota->nama_barang }}</div>

                        <div class="col-4 text-muted">Tanggal</div>
                        <div class="col-8 text-dark">{{ \Carbon\Carbon::parse($nota->tanggal_upload)->format('d F Y') }}</div>
                    </div>

                    <div class="mt-3 pt-2 border-top d-flex justify-content-between align-items-center">
                        <span class="small text-muted" style="font-size: 0.78rem;">Dokumen Lampiran:</span>
                        <a href="{{ route('nota.pdf', $nota->id) }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-danger btn-sm py-1 px-2.5" style="font-size: 0.75rem;">
                            <i class="bi bi-file-earmark-pdf me-1"></i> Preview PDF
                        </a>
                    </div>
                </div>

                {{-- Daftar Penerima --}}
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small fw-bold text-dark text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                        Pilih Kontak Tujuan
                    </span>
                    <a href="{{ route('kontak-bos.index') }}" class="text-decoration-none small text-primary fw-medium" style="font-size: 0.75rem;">
                        <i class="bi bi-gear me-1"></i> Kelola Kontak ({{ $bosses->count() }})
                    </a>
                </div>

                @if($bosses->isNotEmpty())
                    <div class="d-flex flex-column gap-2 mb-3">
                        @foreach($bosses as $b)
                            <div class="p-3 rounded-3 d-flex justify-content-between align-items-center border" style="background-color: #ffffff;">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-primary fw-bold me-3 flex-shrink-0" 
                                         style="width: 38px; height: 38px; background-color: #f0f7ff; border: 1px solid #cce3fd; font-size: 0.8rem;">
                                        {{ strtoupper(substr($b->nama, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark small mb-0.5" style="line-height: 1.2;">{{ $b->nama }}</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">{{ $b->jabatan ?? 'Pimpinan' }} &bull; {{ $b->nomor_wa }}</div>
                                    </div>
                                </div>

                                {{-- Link WhatsApp Langsung (Universal Link untuk iPhone Safari & Android) --}}
                                <a href="{{ $nota->getWaBossUrl($b) }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="btn btn-outline-success btn-sm py-1.5 px-3 d-inline-flex align-items-center fw-medium"
                                   style="font-size: 0.78rem;">
                                    <i class="bi bi-whatsapp me-1.5"></i> Kirim
                                </a>
                            </div>
                        @endforeach
                    </div>

                    {{-- Tombol Buka Sekaligus --}}
                    <div class="d-grid">
                        <button type="button"
                                class="btn btn-brand-primary py-2 fw-semibold d-inline-flex align-items-center justify-content-center gap-2"
                                onclick="kirimSemuaBos{{ $nota->id }}()">
                            <i class="bi bi-box-arrow-up-right"></i>
                            <span>Buka Chat Semua Kontak Sekaligus</span>
                        </button>
                    </div>
                @else
                    <div class="text-center py-4 text-muted border rounded-3 bg-light mb-3">
                        <i class="bi bi-people fs-2 d-block mb-2 text-secondary"></i>
                        <p class="small mb-2">Belum ada nomor WhatsApp pimpinan yang terdaftar.</p>
                        <a href="{{ route('kontak-bos.index') }}" class="btn btn-brand-primary btn-sm px-3">
                            <i class="bi bi-plus-lg me-1"></i> Tambah Kontak Sekarang
                        </a>
                    </div>
                @endif
            </div>

            <div class="modal-footer bg-light border-top py-2.5 px-4 d-flex justify-content-between">
                <a href="{{ route('kontak-bos.index') }}" class="text-muted small text-decoration-none" style="font-size: 0.72rem;">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Kontak Baru
                </a>
                <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
if (typeof kirimSemuaBos{{ $nota->id }} === 'undefined') {
    function kirimSemuaBos{{ $nota->id }}() {
        const daftarUrl = [
            @foreach($bosses as $b)
                "{{ $nota->getWaBossUrl($b) }}",
            @endforeach
        ];

        daftarUrl.forEach((url, index) => {
            setTimeout(() => {
                window.open(url, '_blank', 'noopener,noreferrer');
            }, index * 600);
        });
    }
}
</script>
