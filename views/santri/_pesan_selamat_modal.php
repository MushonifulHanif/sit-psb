<!-- Modal Pesan Selamat (Simplified Modern) -->
<div x-data="{ openPesan: false }" x-init="setTimeout(() => openPesan = true, 500)">
    
    <!-- Backdrop -->
    <div x-show="openPesan" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/40 backdrop-blur-sm z-[60]" x-cloak></div>
    
    <!-- Modal -->
    <div x-show="openPesan" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         x-cloak 
         class="fixed inset-0 z-[70] flex items-center justify-center p-6">
        
        <div class="bg-white/90 backdrop-blur-xl border border-white/40 shadow-2xl rounded-[2.5rem] max-w-md w-full overflow-hidden transform transition-all" @click.away="openPesan = false">
            
            <div class="p-8 sm:p-10">
                <h3 class="text-2xl font-black text-gray-900 tracking-tight mb-6">
                    Pendaftaran Selesai
                </h3>

                <div class="space-y-6">
                    <div class="text-gray-600 text-sm leading-relaxed">
                        <?= nl2br(htmlspecialchars($settings['pesan_selamat'] ?? 'Alhamdulillah, proses pendaftaran telah selesai.')) ?>
                    </div>
                    
                    <div class="bg-emerald-50/50 border border-emerald-100 rounded-3xl p-6 text-center">
                        <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-1">Jadwal Masuk Pondok</p>
                        <div class="text-xl font-black text-emerald-700">
                            <?php
                            $tgl = $settings['tanggal_masuk_pondok'] ?? '';
                            echo format_indo_date($tgl);
                            ?>
                        </div>
                    </div>
                </div>

                <div class="mt-8">
                    <button type="button" 
                            @click="openPesan = false"
                            class="w-full bg-emerald-600 text-white font-black py-4 rounded-2xl shadow-lg shadow-emerald-100 hover:bg-emerald-700 transition-all transform active:scale-95 uppercase tracking-widest text-xs">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
