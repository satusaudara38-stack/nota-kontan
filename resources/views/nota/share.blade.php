
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kirim Nota via WhatsApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card shadow-sm mx-auto" style="max-width:600px">
        <div class="card-body p-4">
            <h3 class="mb-4">Kirim Nota via WhatsApp</h3>

            <p>
                <strong>Nama file:</strong>
                {{ $nota->nama_file }}
            </p>
            <p>
                <strong>Tanggal upload:</strong>
                {{ $nota->tanggal_upload->format('d-m-Y') }}
            </p>

            <form id="waForm">
                <div class="mb-3">
                    <label class="form-label">Nomor WhatsApp Tujuan</label>
                    <input type="tel"
                           id="nomor"
                           class="form-control"
                           placeholder="Contoh: 081234567890"
                           required>
                    <small class="text-muted">
                        Masukkan nomor WhatsApp aktif. Nomor Indonesia boleh
                        diawali 08 atau 62.
                    </small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Pesan</label>
                    <textarea id="pesan"
                              class="form-control"
                              rows="4"
                              required>Halo, berikut informasi nota kontan.
Nama file: {{ $nota->nama_file }}
Tanggal upload: {{ $nota->tanggal_upload->format('d-m-Y') }}

Silakan periksa nota kontan melalui tautan berikut:
{{ route('nota.pdf', $nota->id) }}</textarea>
                </div>

                <button type="submit" class="btn btn-success">
                    Kirim via WhatsApp
                </button>

                <a href="{{ route('nota.show', $nota->id) }}"
                   class="btn btn-secondary">
                    Kembali
                </a>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('waForm').addEventListener('submit', function(e) {
    e.preventDefault();

    let nomor = document.getElementById('nomor').value
        .replace(/\D/g, '');

    if (nomor.startsWith('0')) {
        nomor = '62' + nomor.substring(1);
    } else if (nomor.startsWith('8')) {
        nomor = '62' + nomor;
    }

    if (!nomor.startsWith('62') || nomor.length < 10 ||
        nomor.length > 15) {
        alert('Masukkan nomor WhatsApp Indonesia yang valid.');
        return;
    }

    const pesan = document.getElementById('pesan').value;
    const url = 'https://wa.me/089523306619' 
    '?text=' + encodeURIComponent(pesan);

    window.open(url, '_blank');
});
</script>
</body>
</html>