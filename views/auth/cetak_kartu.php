<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Akun Pendaftaran - <?= htmlspecialchars($account['name'] ?? '') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { background: white; }
            .no-print { display: none !important; }
            @page { margin: 1cm; }
        }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f9fafb; display: flex; justify-content: center; padding: 2rem; }
        .card-container { background: white; width: 100%; max-w: 800px; padding: 2rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06); border: 1px solid #e5e7eb; border-radius: 0.5rem; }
    </style>
</head>
<body>

    <div class="card-container text-gray-900 border-2" style="border-color: #10b981;">
        <!-- KOP STRUK -->
        <div class="flex items-center justify-center border-b-2 border-emerald-600 pb-4 mb-6">
            <div class="text-center">
                <h1 class="text-2xl font-extrabold uppercase tracking-widest text-emerald-800">Panitia Penerimaan Santri Baru</h1>
                <h2 class="text-xl font-bold">PPRTQ RAUDLATUL FALAH</h2>
                <p class="text-sm mt-1 text-gray-600">Simpan kartu ini sebagai bukti identitas login pendaftaran Anda.</p>
            </div>
        </div>

        <div class="mb-6">
            <h3 class="text-lg font-bold bg-emerald-100 text-emerald-800 px-3 py-1 inline-block rounded">KARTU IDENTITAS LOGIN</h3>
        </div>

        <div class="grid grid-cols-3 gap-4 mb-8">
            <div class="col-span-1 font-semibold text-gray-600">Nama Lengkap</div>
            <div class="col-span-2 font-bold text-lg border-b border-gray-200 pb-1">: <?= htmlspecialchars($account['name'] ?? '') ?></div>

            <div class="col-span-1 font-semibold text-gray-600 mt-4">Username (No. Tes)</div>
            <div class="col-span-2 font-bold text-2xl text-emerald-700 mt-4 border-b border-gray-200 pb-1">: <?= htmlspecialchars($account['username'] ?? '') ?></div>

            <div class="col-span-1 font-semibold text-gray-600 mt-2">Password</div>
            <div class="col-span-2 font-mono font-bold text-xl mt-2 border-b border-gray-200 pb-1">: <?= htmlspecialchars($account['password'] ?? '') ?></div>
        </div>

        <!-- PERINGATAN / INFO -->
        <div class="bg-yellow-50 border border-yellow-200 p-4 rounded text-sm text-yellow-800 mb-8">
            <p class="font-bold mb-1">⚠️ PENTING:</p>
            <ul class="list-disc leading-relaxed ml-5">
                <li>Kartu ini berisi <strong>Password Anda</strong>. Harap simpan kartu ini baik-baik dan jangan berikan password Anda kepada pihak lain yang tidak berkepentingan.</li>
                <li>Gunakan Username dan Password di atas untuk <strong>Login</strong> ke aplikasi Pendaftaran Santri Baru.</li>
                <li>Segera masuk ke akun Anda dan lengkapi data administratif yang dibutuhkan.</li>
            </ul>
        </div>

        <div class="flex justify-between items-end mt-12 mb-4">
            <div class="text-sm text-gray-500">
                <i>Dicetak otomatis oleh Sistem SIT-PSB</i><br>
                Tanggal: <?= date('d M Y H:i') ?>
            </div>
            <div class="text-center">
                <p class="text-sm">Panitia PSB,</p>
                <br><br><br>
                <p class="font-bold underline">Panitia Pusat SIT-PSB</p>
            </div>
        </div>
    </div>

    <!-- Panel Tombol (Hanya terlihat di layar) -->
    <div class="no-print fixed bottom-4 right-4 bg-white p-4 shadow-xl border border-gray-200 rounded-xl flex space-x-3">
        <button onclick="window.print()" class="bg-emerald-600 text-white px-4 py-2 rounded-lg font-bold shadow hover:bg-emerald-700">🖨️ Cetak Kartu</button>
        <button onclick="window.close()" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg font-bold shadow hover:bg-gray-300">Tutup</button>
    </div>

    <script>
        // Trigger print dialog as soon as page loads
        window.onload = function() {
            setTimeout(() => {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
