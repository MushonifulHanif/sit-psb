<?php ob_start(); ?>
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
    <div class="absolute -top-24 -left-24 w-96 h-96 bg-emerald-200 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob"></div>
    <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-teal-200 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob animation-delay-2000"></div>

    <div class="max-w-md w-full bg-white/80 backdrop-blur-xl p-10 rounded-[2rem] shadow-2xl border border-white/50 relative z-10 text-center">
        <div class="mx-auto w-24 h-24 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mb-6 shadow-solid-emerald animate-bounce">
            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
        </div>
        
        <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight mb-2">
            Pendaftaran Berhasil!
        </h2>
        <p class="text-gray-500 mb-8">
            Akun Anda berhasil dibuat. Harap catat dan **unduh Kartu Akun** Anda untuk keperluan Ujian dan Login.
        </p>

        <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 mb-8 text-left space-y-4">
            <div>
                <p class="text-xs text-gray-500 font-bold uppercase tracking-wide">Nama Lengkap</p>
                <p class="text-lg font-bold text-gray-900"><?= htmlspecialchars($account['name'] ?? '') ?></p>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-bold uppercase tracking-wide">Username (Nomor Tes)</p>
                <div class="bg-white p-3 rounded-lg border border-gray-200 mt-1 flex justify-between items-center">
                    <span class="text-xl font-mono font-bold text-emerald-700 tracking-wider"><?= htmlspecialchars($account['username'] ?? '') ?></span>
                </div>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-bold uppercase tracking-wide">Password Anda</p>
                <div class="bg-emerald-50 text-emerald-700 p-3 rounded-lg border border-emerald-100 mt-1">
                    <span class="text-lg font-mono font-bold"><?= htmlspecialchars($account['password'] ?? '') ?></span>
                </div>
                <p class="text-xs text-red-500 mt-2 font-medium flex items-start">
                    <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Password ini hanya dimunculkan 1 kali. Segera cetak kartu atau simpan!
                </p>
            </div>
        </div>

        <div class="space-y-4">
            <button type="button" id="btn-download" onclick="downloadCard()" class="w-full flex justify-center items-center py-3 px-4 border border-transparent text-sm font-bold rounded-full text-white bg-emerald-600 hover:bg-emerald-700 transition-all shadow-md hover:shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                Download Kartu Akun (.PNG)
            </button>
            
            <a href="<?= url('auth/login') ?>" class="w-full flex justify-center py-3 px-4 border border-gray-300 text-sm font-bold rounded-full text-gray-700 bg-white hover:bg-gray-50 transition-all">
                Lanjut ke Halaman Login
            </a>
        </div>
    </div>
</div>

<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>

<!-- Hidden Card Template for Capture -->
<div id="card-template" class="fixed -left-[9999px] top-0">
    <div id="capture-area" class="bg-white p-10 w-[800px] border-4 border-emerald-600 rounded-[3rem] overflow-hidden shadow-2xl text-left">
        <div class="flex items-center justify-between border-b-4 border-emerald-600 pb-6 mb-8">
            <div class="w-16 h-16 bg-emerald-600 rounded-2xl flex items-center justify-center text-white">
                <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253" /></svg>
            </div>
            <div class="text-right">
                <h1 class="text-2xl font-black text-emerald-800 uppercase tracking-widest">SIT-PSB ONLINE</h1>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-[0.2em]">PPRTQ Raudlatul Falah</p>
            </div>
        </div>
        
        <div class="mb-10">
            <h2 class="text-xl font-black bg-emerald-50 text-emerald-700 px-4 py-2 inline-block rounded-xl mb-6 uppercase tracking-wider">Kartu Identitas Pendaftar</h2>
            <div class="grid grid-cols-3 gap-6">
                <div class="col-span-1 text-sm font-black text-gray-400 uppercase tracking-widest">Nama Lengkap</div>
                <div class="col-span-2 text-xl font-black text-gray-900">: <?= htmlspecialchars($account['name'] ?? '') ?></div>
                
                <div class="col-span-1 text-sm font-black text-gray-400 uppercase tracking-widest">Nomor Tes</div>
                <div class="col-span-2 text-3xl font-black text-emerald-600">: <?= htmlspecialchars($account['username'] ?? '') ?></div>
                
                <div class="col-span-1 text-sm font-black text-gray-400 uppercase tracking-widest">Password</div>
                <div class="col-span-2 text-2xl font-black text-gray-800 font-mono">: <?= htmlspecialchars($account['password'] ?? '') ?></div>
            </div>
        </div>

        <div class="bg-amber-50 border-2 border-amber-200 p-6 rounded-3xl text-amber-900">
            <p class="font-black text-xs uppercase tracking-[0.2em] mb-3 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                Penting
            </p>
            <p class="text-sm font-bold leading-relaxed">
                Simpan kartu ini. Gunakan Nomor Tes dan Password di atas untuk login ke Dashboard Santri dan mengikuti Ujian Seleksi.
            </p>
        </div>

        <div class="mt-12 flex justify-between items-end border-t border-gray-100 pt-6">
            <div class="text-[10px] text-gray-400 font-black uppercase tracking-widest">
                Dicetak pada: <?= date('d/m/Y H:i') ?>
            </div>
            <div class="text-center">
                <div class="w-32 h-1 bg-gray-100 mb-2 mx-auto"></div>
                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Panitia Seleksi Pusat</p>
            </div>
        </div>
    </div>
</div>

<script>
    function downloadCard() {
        const btn = document.getElementById('btn-download');
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<svg class="animate-spin h-5 w-5 mr-2 inline" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Menyiapkan...';
        btn.disabled = true;

        const area = document.getElementById('capture-area');
        
        html2canvas(area, {
            scale: 2,
            useCORS: true,
            backgroundColor: '#ffffff'
        }).then(canvas => {
            const link = document.createElement('a');
            link.download = 'Kartu_PSB_<?= $account['username'] ?>.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
            
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        });
    }
</script>

<style>
    .animate-blob { animation: blob 7s infinite; }
    .animation-delay-2000 { animation-delay: 2s; }
    @keyframes blob {
        0% { transform: translate(0px, 0px) scale(1); }
        33% { transform: translate(30px, -50px) scale(1.1); }
        66% { transform: translate(-20px, 20px) scale(0.9); }
        100% { transform: translate(0px, 0px) scale(1); }
    }
</style>
<?php
$content = ob_get_clean();
$title = "Pendaftaran Berhasil - SIT-PSB";
require __DIR__ . '/../layouts/guest.php';
?>
