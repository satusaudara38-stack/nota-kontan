{{-- Modal Kirim / Konfirmasi WA ke Customer --}}
<div class="modal fade" id="modalKirimCustomer{{ $nota->id }}" tabindex="-1" aria-labelledby="modalLabelCustomer{{ $nota->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content card-custom shadow-lg border-0">
            {{-- Modal Header --}}
            <div class="modal-header border-bottom py-3 px-4" style="background-color: #0f172a; color: #ffffff;">
                <div>
                    <h5 class="modal-title fs-6 fw-bold mb-0" id="modalLabelCustomer{{ $nota->id }}">
                        Konfirmasi WhatsApp ke Customer #{{ str_pad($nota->id, 5, '0', STR_PAD_LEFT) }}
                    </h5>
                    <div class="small text-white-50" style="font-size: 0.75rem;">
                        Pengiriman dokumen faktur & konfirmasi pemesanan digital
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-3 p-md-4" style="-webkit-overflow-scrolling: touch; overflow-y: auto;">
                {{-- Status Pengiriman ke Customer Saat Ini --}}
                @if($nota->isTerkirimCustomer())
                    <div class="alert alert-success d-flex align-items-center justify-content-between py-2.5 px-3 mb-3 small border-0 shadow-sm" style="background-color: #ecfdf5; color: #065f46; border-radius: 8px;">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-circle-fill text-success fs-5"></i>
                            <div>
                                <strong class="d-block" style="font-size: 0.82rem;">Status: Sudah Terkirim ke Customer</strong>
                                <span class="text-muted" style="font-size: 0.73rem;">
                                    Terakhir: {{ $nota->wa_customer_sent_at->format('d/m/Y H:i') }} WIB &bull; Total: {{ $nota->wa_customer_sent_count }}x
                                </span>
                            </div>
                        </div>
                        <form action="{{ route('nota.mark-sent', $nota->id) }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="target" value="customer">
                            <input type="hidden" name="action" value="reset">
                            <button type="submit" class="btn btn-sm btn-link text-muted p-0 text-decoration-none" style="font-size: 0.72rem;" title="Reset status ke belum terkirim">
                                <i class="bi bi-arrow-counterclockwise"></i> Reset
                            </button>
                        </form>
                    </div>
                @else
                    <div class="alert alert-light border d-flex align-items-center justify-content-between py-2.5 px-3 mb-3 small text-muted" style="border-radius: 8px;">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-clock-history text-secondary"></i>
                            <span style="font-size: 0.78rem;">Status: <strong>Belum pernah dikirim ke Customer</strong></span>
                        </div>
                        <form action="{{ route('nota.mark-sent', $nota->id) }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="target" value="customer">
                            <input type="hidden" name="action" value="sent">
                            <button type="submit" class="btn btn-sm btn-outline-success py-1 px-2.5 fw-semibold" style="font-size: 0.75rem;">
                                <i class="bi bi-check2"></i> Tandai Sudah
                            </button>
                        </form>
                    </div>
                @endif

                {{-- Detail Penerima Customer --}}
                <div class="p-3 rounded-3 mb-3" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                        <span class="text-uppercase text-muted fw-bold" style="font-size: 0.7rem; letter-spacing: 0.05em;">
                            Data Customer Penerima
                        </span>
                        <span class="badge {{ $nota->isSparepart() ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-primary-subtle text-primary border border-primary-subtle' }}">
                            {{ $nota->kategori_label }}
                        </span>
                    </div>

                    <div class="row g-1 small">
                        <div class="col-4 text-muted">Nama Customer</div>
                        <div class="col-8 fw-bold text-dark">{{ $nota->nama_customer }}</div>

                        <div class="col-4 text-muted">Nomor WhatsApp</div>
                        <div class="col-8 text-dark fw-semibold">
                            @if($nota->nomor_wa)
                                <i class="bi bi-whatsapp text-success me-1"></i>{{ $nota->nomor_wa }}
                            @else
                                <span class="text-danger"><i class="bi bi-exclamation-circle me-1"></i>Belum ada nomor WA</span>
                            @endif
                        </div>

                        @if(!$nota->isSparepart())
                            <div class="col-4 text-muted">Unit & Warna</div>
                            <div class="col-8 text-dark">{{ $nota->unit_type }} ({{ $nota->warna ?: '-' }})</div>
                        @else
                            <div class="col-4 text-muted">Rincian Part</div>
                            <div class="col-8 text-dark">{{ $nota->nama_barang }}</div>
                        @endif

                        <div class="col-4 text-muted">Total Bayar</div>
                        <div class="col-8 text-success fw-bold">Rp {{ $nota->formatted_total_bayar }}</div>
                    </div>
                </div>

                {{-- Pratinjau Teks Pesan WhatsApp --}}
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1.5">
                        <span class="small fw-bold text-dark text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                            <i class="bi bi-chat-text text-success me-1"></i> Teks Pesan yang Dikirim
                        </span>
                        <button type="button" class="btn btn-link btn-sm text-secondary p-0 text-decoration-none" style="font-size: 0.72rem;" onclick="navigator.clipboard.writeText(document.getElementById('teksWaCust{{ $nota->id }}').innerText); this.innerHTML='<i class=\'bi bi-check2\'></i> Tersalin'; setTimeout(() => this.innerHTML='<i class=\'bi bi-clipboard\'></i> Salin Teks', 2000);">
                            <i class="bi bi-clipboard"></i> Salin Teks
                        </button>
                    </div>
                    <div id="teksWaCust{{ $nota->id }}" class="p-2.5 rounded-3 bg-light border" style="font-family: monospace; font-size: 0.74rem; white-space: pre-wrap; line-height: 1.45; max-height: 180px; overflow-y: auto; color: #334155;">{{ $nota->getCustomerWaMessage() }}</div>
                </div>

                {{-- Tombol Aksi Kirim / Buka WhatsApp --}}
                @if($nota->nomor_wa)
                    <div class="d-grid gap-2">
                        <a href="{{ route('nota.share', $nota->id) }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           onclick="if(typeof updateBadgeCustomerTerkirim==='function'){ updateBadgeCustomerTerkirim({{ $nota->id }}); }"
                           class="btn btn-success py-2 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 shadow-sm">
                            <i class="bi bi-whatsapp fs-5"></i>
                            <span>{{ $nota->isTerkirimCustomer() ? 'Kirim Ulang Pesan via WhatsApp' : 'Buka WhatsApp & Kirim Pesan' }}</span>
                        </a>

                        <form action="{{ route('nota.mark-sent', $nota->id) }}" method="POST" class="d-grid">
                            @csrf
                            <input type="hidden" name="target" value="customer">
                            <input type="hidden" name="action" value="{{ $nota->isTerkirimCustomer() ? 'reset' : 'sent' }}">
                            <button type="submit" class="btn btn-outline-secondary btn-sm py-1.5 fw-medium">
                                <i class="bi {{ $nota->isTerkirimCustomer() ? 'bi-arrow-counterclockwise text-warning' : 'bi-check2 text-success' }} me-1"></i>
                                {{ $nota->isTerkirimCustomer() ? 'Reset Status (Ubah ke Belum Terkirim)' : 'Tandai Selesai Dikirim Manual' }}
                            </button>
                        </form>
                    </div>
                @else
                    <div class="alert alert-warning py-2 px-3 small mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle"></i>
                        <span>Nomor WhatsApp customer belum diisi. Silakan edit transaksi untuk menambahkan nomor.</span>
                    </div>
                @endif
            </div>

            <div class="modal-footer bg-light border-top py-2.5 px-4 d-flex justify-content-between">
                <a href="{{ route('nota.edit', $nota->id) }}" class="text-muted small text-decoration-none" style="font-size: 0.72rem;">
                    <i class="bi bi-pencil me-1"></i> Edit Data Customer
                </a>
                <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
