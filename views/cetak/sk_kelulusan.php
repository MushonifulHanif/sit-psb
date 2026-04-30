<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keputusan Kelulusan - <?= htmlspecialchars($data['name']) ?></title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; line-height: 1.6; color: #000; background: #fff; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; padding: 40px; border: 1px solid #ddd; }
        .kop-surat { text-align: center; border-bottom: 3px solid #000; padding-bottom: 20px; margin-bottom: 30px; }
        .kop-surat h1 { margin: 0 0 5px 0; font-size: 24px; text-transform: uppercase; }
        .kop-surat h2 { margin: 0 0 5px 0; font-size: 20px; font-weight: normal; }
        .kop-surat p { margin: 0; font-size: 14px; }
        .judul-surat { text-align: center; margin-bottom: 30px; }
        .judul-surat h3 { margin: 0; font-size: 18px; text-decoration: underline; }
        .judul-surat p { margin: 5px 0 0 0; }
        .content { margin-bottom: 40px; }
        .content p { text-indent: 40px; text-align: justify; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; margin-left: 40px; }
        table td { padding: 5px; vertical-align: top; }
        table td:first-child { width: 150px; }
        .ttd { float: right; text-align: center; width: 300px; }
        .ttd p { margin: 0; }
        .ttd .nama { font-weight: bold; text-decoration: underline; margin-top: 80px; }
        @media print {
            body { padding: 0; }
            .container { border: none; padding: 0; }
            button { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <button onclick="window.print()" style="position:fixed; top:20px; right:20px; padding:10px 20px; background:#10b981; color:white; border:none; border-radius:5px; cursor:pointer;">CETAK</button>

    <div class="container">
        <div class="kop-surat">
            <h2>PANITIA PENERIMAAN SANTRI BARU (PSB)</h2>
            <h1><?= htmlspecialchars($nama_pesantren) ?></h1>
            <p>Tahun Ajaran <?= htmlspecialchars($tahun_ajaran) ?></p>
        </div>

        <div class="judul-surat">
            <h3>SURAT KEPUTUSAN KELULUSAN</h3>
            <p>Nomor: SK-PSB/<?= date('Y') ?>/<?= str_pad($data['id'], 3, '0', STR_PAD_LEFT) ?></p>
        </div>

        <div class="content">
            <p>Berdasarkan hasil Ujian Seleksi Penerimaan Santri Baru (PSB) <?= htmlspecialchars($nama_pesantren) ?> Tahun Ajaran <?= htmlspecialchars($tahun_ajaran) ?>, maka Panitia PSB memutuskan bahwa peserta dengan identitas di bawah ini:</p>
            
            <table>
                <tr>
                    <td>Nomor Tes</td>
                    <td>: <b><?= htmlspecialchars($data['username']) ?></b></td>
                </tr>
                <tr>
                    <td>Nama Lengkap</td>
                    <td>: <b><?= htmlspecialchars($data['name']) ?></b></td>
                </tr>
                <tr>
                    <td>Jenis Kelamin</td>
                    <td>: <?= $data['jk'] == 'L' ? 'Laki-laki (Putra)' : 'Perempuan (Putri)' ?></td>
                </tr>
                <tr>
                    <td>Asal Sekolah</td>
                    <td>: <?= htmlspecialchars($data['asal_sekolah']) ?></td>
                </tr>
            </table>

            <p style="text-align: center; font-size: 20px; font-weight: bold; padding: 20px 0; border-top: 1px dashed #ccc; border-bottom: 1px dashed #ccc;">
                DINYATAKAN <span style="color: #10b981;">LULUS / DITERIMA</span>
            </p>

            <p>Sebagai santri baru di <?= htmlspecialchars($nama_pesantren) ?> Tahun Ajaran <?= htmlspecialchars($tahun_ajaran) ?>.</p>
            <p>Keputusan ini bersifat mutlak dan tidak dapat diganggu gugat. Surat keputusan ini dapat digunakan sebagai lampiran persyaratan Daftar Ulang.</p>
        </div>

        <div class="ttd">
            <p>Pati, <?= date('d F Y') ?></p>
            <p>Ketua Panitia PSB,</p>
            <div class="nama">H. Panitia Ketuawan, S.Pd.I</div>
        </div>
        
        <div style="clear: both;"></div>
    </div>
</body>
</html>
