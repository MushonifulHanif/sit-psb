<div x-data="{ 
    isRevealed: localStorage.getItem('result_revealed_<?= $user['id'] ?>') === 'true', 
    isLoading: false, 
    loadingStep: 0,
    steps: [
        'Synchronizing Academic Scores...',
        'Validating Inspector Verification...',
        'Finalizing Executive Decision...'
    ],
    revealResults() {
        this.isLoading = true;
        this.loadingStep = 0;
        let interval = setInterval(() => {
            this.loadingStep++;
            if (this.loadingStep >= this.steps.length) {
                clearInterval(interval);
                this.isLoading = false;
                this.isRevealed = true;
                localStorage.setItem('result_revealed_<?= $user['id'] ?>', 'true');
                this.triggerConfetti();
            }
        }, 1500);
    },
    triggerConfetti() {
        if (typeof confetti === 'function' && '<?= $status ?>' === 'lulus') {
            confetti({
                particleCount: 150,
                spread: 70,
                origin: { y: 0.6 }
            });
        }
    }
}">

<?php if ($status == 'sudah_ujian'): ?>
    <div class="max-w-2xl mx-auto py-12">
        <div class="bg-white rounded-[2.5rem] border border-emerald-100 shadow-2xl shadow-emerald-50 overflow-hidden relative">
            <div class="absolute top-0 left-0 w-full h-2 bg-emerald-500"></div>
            <div class="p-10 text-center">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-emerald-50 rounded-3xl mb-8 relative">
                    <svg class="w-12 h-12 text-emerald-600 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                </div>
                
                <h3 class="text-3xl font-black text-gray-900 mb-4 tracking-tight">Jawaban Sedang Diverifikasi</h3>
                <p class="text-gray-600 text-lg leading-relaxed mb-8">Terima kasih telah menyelesaikan ujian CBT. Saat ini, seluruh jawaban Anda sedang dalam proses penilaian dan verifikasi akhir oleh tim **Mufatis** dan **Panitia PSB**.</p>
                
                <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 inline-block text-left w-full max-w-md mx-auto">
                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-3">Estimasi Waktu</p>
                    <div class="flex items-center text-emerald-700 font-bold">
                        <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Maksimal 1x24 Jam Kerja
                    </div>
                </div>

                <div class="mt-10 pt-8 border-t border-dashed border-gray-100">
                    <p class="text-sm text-gray-400 italic font-medium">Pengumuman kelulusan akan muncul secara otomatis di halaman ini setelah proses verifikasi selesai.</p>
                </div>
            </div>
        </div>
    </div>
<?php elseif (in_array($status, ['lulus', 'gagal', 'daftar_ulang', 'selesai'])): ?>
    
    <!-- Script Confetti -->
    <script src="<?= asset('js/confetti.min.js') ?>"></script>

    <!-- Initial State: Hidden Results -->
    <div x-show="!isRevealed && !isLoading" class="text-center py-20 bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200">
        <div class="mx-auto w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center mb-6">
            <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Hasil Seleksi Telah Tersedia</h2>
        <p class="text-gray-500 mb-8">Keputusan akhir telah ditetapkan oleh Pengasuh Pondok Pesantren.</p>
        <button @click="revealResults()" class="inline-flex items-center px-8 py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-full shadow-lg shadow-emerald-100 transition-all transform hover:-translate-y-1">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
            Lihat Hasil Pengumuman
        </button>
    </div>

    <!-- Loading Simulation -->
    <div x-show="isLoading" x-cloak class="py-20 text-center">
        <div class="max-w-xs mx-auto">
            <div class="relative pt-1">
                <div class="flex mb-4 items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-emerald-600 bg-emerald-200">
                            Technical Verification
                        </span>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-semibold inline-block text-emerald-600" x-text="Math.round(((loadingStep + 1) / steps.length) * 100) + '%'"></span>
                    </div>
                </div>
                <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-emerald-100">
                    <div :style="'width: ' + (((loadingStep + 1) / steps.length) * 100) + '%'" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-emerald-500 transition-all duration-500"></div>
                </div>
            </div>
            <p class="text-sm font-mono text-gray-500 animate-pulse" x-text="steps[loadingStep]"></p>
        </div>
    </div>

    <!-- Final Result: Lulus -->
    <div x-show="isRevealed && '<?= $status ?>' !== 'gagal'" x-cloak x-transition:enter="transition ease-out duration-1000" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100">
        <div class="text-center py-12 bg-emerald-50 border border-emerald-200 rounded-[2rem] relative overflow-hidden shadow-inner">
            <div class="absolute inset-0 opacity-5 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
            <span class="text-7xl inline-block mb-6 relative z-10">🎊</span>
            <h3 class="text-4xl font-black text-emerald-900 mb-2 relative z-10 tracking-tight">ALHAMDULILLAH, ANDA LULUS!</h3>
            <p class="text-emerald-700 text-lg mb-8 relative z-10 max-w-lg mx-auto font-medium">Selamat! Anda dinyatakan diterima sebagai santri baru PPRTQ Raudlatul Falah Tahun Ajaran <?= htmlspecialchars($settings['tahun_ajaran'] ?? '') ?>.</p>
            
            <div class="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-4 relative z-10">
                <a href="<?= url('santri/cetak-sk') ?>" target="_blank" class="inline-flex items-center bg-white text-emerald-700 border border-emerald-200 hover:bg-emerald-100 font-bold py-3 px-8 rounded-full shadow-sm transition-all transform hover:scale-105">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    Cetak SK Kelulusan
                </a>

                <?php if (in_array($status, ['lulus', 'daftar_ulang'])): ?>
                <button @click="activeTab = 4" class="inline-flex items-center bg-emerald-600 text-white hover:bg-emerald-700 font-bold py-3 px-8 rounded-full shadow-md transition-all transform hover:scale-105">
                    Lanjut Ke Daftar Ulang
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Final Result: Gagal -->
    <div x-show="isRevealed && '<?= $status ?>' === 'gagal'" x-cloak class="text-center py-20 bg-red-50 border border-red-100 rounded-[2rem]">
        <span class="text-6xl inline-block mb-6">🙏</span>
        <h3 class="text-3xl font-black text-red-900 mb-4 tracking-tight">MOHON MAAF, ANDA BELUM LULUS</h3>
        <p class="text-red-700 max-w-md mx-auto text-lg leading-relaxed">
            Berdasarkan hasil seleksi administratif dan ujian seleksi, kami belum bisa menerima Anda pada kesempatan kali ini. Tetap semangat dan teruslah belajar!
        </p>
    </div>

<?php endif; ?>

</div>
