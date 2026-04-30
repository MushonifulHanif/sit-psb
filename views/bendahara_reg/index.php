<?php ob_start(); ?>
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Verifikasi Pembayaran Registrasi</h1>
    <p class="text-gray-600 mt-1">Daftar transfer pembayaran registrasi dari pendaftar.</p>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pendaftar</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nominal</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bukti TF</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($pembayaran as $row): ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <?= date('d/m/Y H:i', strtotime($row['created_at'])) ?>
                    </td>
                    <td class="px-6 py-4 flex flex-col">
                        <span class="text-sm font-medium text-gray-900"><?= htmlspecialchars($row['name']) ?></span>
                        <span class="text-xs text-gray-500"><?= htmlspecialchars($row['username']) ?></span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        Rp <?= number_format($row['nominal_bayar'], 0, ',', '.') ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600">
                        <a href="<?= asset('../uploads/bukti_bayar/' . $row['bukti_transfer']) ?>" @click.prevent="$dispatch('image-preview', { src: $el.href, title: 'Bukti Transfer Registrasi' })" class="hover:underline flex items-center cursor-pointer">
                            <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            Lihat Bukti
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?php if($row['status'] == 'diterima'): ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Diterima</span>
                        <?php elseif($row['status'] == 'ditolak'): ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Ditolak</span>
                        <?php else: ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" x-data="{ openModal: false }">
                        <div class="flex items-center space-x-3">
                            <?php if($row['status'] == 'pending'): ?>
                                <button @click="openModal = true" class="px-4 py-2 bg-indigo-50 text-indigo-700 rounded-xl hover:bg-indigo-100 font-bold transition-all flex items-center">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    Verifikasi
                                </button>
                            <?php else: ?>
                                <span class="px-3 py-1.5 bg-gray-50 text-gray-400 rounded-lg text-xs font-bold uppercase tracking-wider border border-gray-100">Selesai</span>
                            <?php endif; ?>
                            
                            <?php 
                                $no_wa = preg_replace('/[^0-9]/', '', $row['no_wa'] ?? '');
                                if(strpos($no_wa, '0') === 0) $no_wa = '62' . substr($no_wa, 1);
                                
                                $pesan_wa = "Assalamu'alaikum Wr Wrb, Ayah/Bunda dari *" . $row['name'] . "*. Kami dari Bendahara Registrasi PPRTQ Raudlatul Falah mengonfirmasi bahwa pembayaran pendaftaran Anda telah kami verifikasi dan dinyatakan VALID. Selamat! Langkah selanjutnya adalah mengikuti tes seleksi. Mohon tunggu informasi jadwal tes dari Sekretaris kami. Terimakasih.";
                                $url_wa = "https://wa.me/{$no_wa}?text=" . urlencode($pesan_wa);
                            ?>
                            <a href="<?= $url_wa ?>" target="_blank" class="p-2 bg-emerald-100 text-emerald-700 rounded-xl hover:bg-emerald-200 transition-all shadow-sm shadow-emerald-100" title="Hubungi via WhatsApp">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.067 2.877 1.215 3.076.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.812 1.056 3.882 1.614 5.99 1.616h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                </svg>
                            </a>
                        </div>
                            
                            <!-- Verifikasi Modal -->
                            <div x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/20 backdrop-blur-sm" x-cloak>
                                <div x-show="openModal" class="bg-white/70 backdrop-blur-xl border border-white/40 shadow-2xl rounded-[2.5rem] max-w-lg w-full relative transition-all transform scale-100 whitespace-normal" @click.away="openModal = false">
                                    <form action="<?= url("bendahara-reg/verifikasi/{$row['id']}") ?>" method="POST">
                                        <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                                        <div class="px-8 py-6 border-b border-gray-100">
                                            <h3 class="text-xl font-black text-gray-900">Verifikasi Pembayaran</h3>
                                        </div>
                                        <div class="p-8">
                                            <div class="mb-6 bg-white/50 backdrop-blur-sm p-5 rounded-3xl border border-white/60 shadow-sm">
                                                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Informasi Santri</p>
                                                <p class="text-sm font-black text-gray-900"><?= htmlspecialchars($row['name']) ?></p>
                                                <p class="text-lg font-black text-emerald-600 mt-1">Rp <?= number_format($row['nominal_bayar'], 0, ',', '.') ?></p>
                                            </div>
                                            
                                            <div class="mb-6">
                                                <label class="block text-[10px] font-black uppercase tracking-widest text-indigo-700 mb-2 ml-1">Status Verifikasi</label>
                                                <div class="relative group" x-data="{ 
                                                    open: false, 
                                                    status: 'diterima',
                                                    options: [
                                                        { label: '✅ Terima (LUNAS)', value: 'diterima' },
                                                        { label: '❌ Tolak', value: 'ditolak' }
                                                    ],
                                                    get selectedLabel() {
                                                        return this.options.find(o => o.value == this.status)?.label || 'Pilih Status'
                                                    }
                                                }">
                                                    <input type="hidden" name="status" :value="status" required>
                                                    <button type="button" @click="open = !open" @click.away="open = false"
                                                            class="w-full flex items-center justify-between px-5 py-4 border border-gray-200 rounded-2xl text-sm transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white/50 hover:bg-white font-black">
                                                        <span x-text="selectedLabel" :class="status == 'diterima' ? 'text-green-700' : 'text-red-700'"></span>
                                                        <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                                    </button>

                                                    <div x-show="open" x-transition x-cloak
                                                         class="absolute z-[70] left-0 right-0 mt-2 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden p-2">
                                                        <template x-for="item in options" :key="item.value">
                                                            <div @click="status = item.value; open = false" 
                                                                 class="flex items-center justify-between px-5 py-3 rounded-xl cursor-pointer hover:bg-gray-50 transition-all font-black"
                                                                 :class="status == item.value ? 'bg-gray-50' : ''">
                                                                <span x-text="item.label" class="text-xs whitespace-nowrap" :class="status == item.value ? (item.value == 'diterima' ? 'text-green-700' : 'text-red-700') : 'text-gray-600'"></span>
                                                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all ml-4"
                                                                     :class="status == item.value ? 'border-indigo-500 bg-indigo-500' : 'border-gray-200 bg-white'">
                                                                    <div x-show="status == item.value" x-transition class="w-1.5 h-1.5 bg-white rounded-full"></div>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div>
                                                <label class="block text-[10px] font-black uppercase tracking-widest text-indigo-700 mb-2 ml-1">Catatan</label>
                                                <textarea name="catatan" rows="2" class="w-full border-gray-200 rounded-2xl shadow-sm focus:ring-indigo-500/20 focus:border-indigo-500 p-5 border bg-white/50 hover:bg-white transition-all text-sm font-medium" placeholder="Opsional..."></textarea>
                                            </div>
                                        </div>
                                        <div class="bg-gray-50/50 px-8 py-6 flex justify-end space-x-3 border-t border-gray-100 rounded-b-[2.5rem]">
                                            <button type="button" @click="openModal = false" class="px-6 py-2.5 text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-gray-600 transition-colors">Batal</button>
                                            <button type="submit" class="px-8 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 font-black text-[11px] uppercase tracking-widest shadow-lg shadow-indigo-100 transition-all active:scale-95">Simpan Verifikasi</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if (empty($pembayaran)): ?>
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-gray-500">Belum ada data pembayaran registrasi.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = "Bendahara Registrasi - SIT-PSB";
require __DIR__ . '/../layouts/admin.php';
