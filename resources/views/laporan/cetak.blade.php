<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Hasil Seleksi</title>
    <style>
        @page { margin: 0; size: A4; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; color: #000; margin: 2cm 2.5cm; }
        .header { text-align: center; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 3px double #000; display: flex; align-items: center; justify-content: center; gap: 20px; }
        .logo-box { width: 80px; height: 80px; background: #222; color: #fff; line-height: 80px; font-size: 28px; font-weight: bold; text-align: center; }
        .company-name { font-size: 20pt; font-weight: bold; text-transform: uppercase; margin: 0; }
        .title { text-align: center; font-size: 14pt; font-weight: bold; text-decoration: underline; margin-top: 30px; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        table, th, td { border: 1px solid #000; }
        th { background-color: #f0f0f0; padding: 10px; text-align: center; font-weight: bold; -webkit-print-color-adjust: exact; }
        td { padding: 8px; text-align: center; }
        td.nama { text-align: left; padding-left: 10px; }
        .footer { margin-top: 50px; float: right; width: 250px; text-align: center; }
        .badge { padding: 2px 8px; border: 1px solid #000; border-radius: 4px; font-size: 9pt; font-weight: bold; }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <div class="logo-box">BM</div>
        <div>
            <h1 class="company-name">PT. BHANDAWA METAFORA WARSOYO</h1>
            <p style="margin:0;">Jalan Jendral Sudirman No. Kav 10-11, Jakarta Pusat</p>
        </div>
    </div>

    <div class="title">HASIL KEPUTUSAN SELEKSI SUPPLIER</div>
    <div style="text-align:center; margin-bottom:30px;">Nomor: {{ date('Y') }}/PROC-PURCH/{{ rand(1000,9999) }}</div>

    <p>Berdasarkan hasil verifikasi berkas dan perhitungan nilai metode <strong>SAW</strong>, berikut adalah hasilnya:</p>

    <table>
        <thead>
            <tr>
                <th width="8%">No</th>
                <th width="40%">Nama Supplier</th>
                <th width="20%">Skor Akhir</th>
                <th width="32%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ranking as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="nama"><strong>{{ $row->nama }}</strong></td>
                <td>{{ number_format($row->skor_akhir, 4) }}</td>
                <td>
                    @if($row->status_supplier == 'Lulus') <span class="badge">DITERIMA</span>
                    @elseif($row->status_supplier == 'Gagal') GUGUR
                    @else PENDING @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="4">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Jakarta, {{ date('d F Y') }}</p>
        <p>Mengetahui,<br><strong>Purchasing Manager</strong></p>
        <br><br><br>
        <p><u>( ......................................... )</u></p>
    </div>
</body>
</html>