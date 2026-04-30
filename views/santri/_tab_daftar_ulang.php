<?php
$biaya_du = floatval($settings['biaya_daftar_ulang'] ?? 0);
$sudah_bayar = $total_bayar_du;
$sisa_du = max(0, $biaya_du - $sudah_bayar);
$progress_persen = $biaya_du > 0 ? min(100, ($sudah_bayar / $biaya_du) * 100) : 100;
?>
<div class="max-w-4xl mx-auto">
    
    <div class="bg-white border rounded-lg shadow-sm mb-8 overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-800">📋 Rincian Daftar Ulang</h3>
        </div>
        <div class="p-6">
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-6">
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Biaya Daftar Ulang</dt>
                    <dd class="mt-1 text-2xl font-semibold text-gray-900">Rp <?= number_format($biaya_du, 0, ',', '.') ?></dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Total Dibayar</dt>
                    <dd class="mt-1 text-2xl font-semibold text-emerald-600">Rp <?= number_format($sudah_bayar, 0, ',', '.') ?></dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Progress Pembayaran (Sisa: Rp <?= number_format($sisa_du, 0, ',', '.') ?>)</dt>
                    <dd class="mt-2">
                        <div class="w-full bg-gray-200 rounded-full h-4">
                            <div class="bg-emerald-500 h-4 rounded-full transition-all duration-500" style="width: <?= $progress_persen ?>%"></div>
                        </div>
                        <div class="text-right text-sm font-medium text-gray-600 mt-1"><?= round($progress_persen) ?>%</div>
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <?php if (count($riwayat_du) > 0): ?>
    <div class="mb-12 bg-amber-50/30 rounded-[2.5rem] p-8 border border-amber-100 shadow-sm relative overflow-hidden">
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-amber-100/50 rounded-full blur-3xl"></div>
        
        <h4 class="text-xl font-black text-amber-900 mb-6 flex items-center relative z-10">
            <div class="bg-amber-500 text-white p-2.5 rounded-2xl mr-4 shadow-lg shadow-amber-200 rotate-3">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            Riwayat Pembayaran
        </h4>

        <div class="overflow-hidden border border-amber-200/50 rounded-[2rem] bg-white shadow-xl shadow-amber-900/5 relative z-10">
            <table class="min-w-full divide-y divide-amber-100">
                <thead class="bg-amber-50/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-amber-800 uppercase tracking-[0.2em]">Tanggal</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-amber-800 uppercase tracking-[0.2em]">Biaya DU</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-amber-800 uppercase tracking-[0.2em]">Infaq</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black text-amber-800 uppercase tracking-[0.2em]">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-amber-50">
                    <?php foreach($riwayat_du as $row): ?>
                    <tr class="hover:bg-amber-50/30 transition-colors">
                        <td class="px-6 py-5 whitespace-nowrap text-sm font-bold text-gray-700"><?= format_indo_date($row['created_at']) ?></td>
                        <td class="px-6 py-5 whitespace-nowrap text-sm font-black text-gray-900"><?= format_rupiah($row['nominal_bayar']) ?></td>
                        <td class="px-6 py-5 whitespace-nowrap text-sm font-medium text-amber-600"><?= format_rupiah($row['nominal_infaq']) ?></td>
                        <td class="px-6 py-5 whitespace-nowrap">
                            <?php if($row['status'] == 'diterima'): ?>
                                <span class="px-3 py-1 inline-flex text-[10px] font-black leading-5 rounded-full bg-emerald-100 text-emerald-700 uppercase tracking-widest border border-emerald-200">DITERIMA</span>
                            <?php elseif($row['status'] == 'ditolak'): ?>
                                <span class="px-3 py-1 inline-flex text-[10px] font-black leading-5 rounded-full bg-red-100 text-red-700 uppercase tracking-widest border border-red-200" title="<?= htmlspecialchars($row['catatan_verifikasi']) ?>">DITOLAK</span>
                            <?php else: ?>
                                <span class="px-4 py-1.5 inline-flex text-[10px] font-black leading-5 rounded-full bg-amber-500 text-white uppercase tracking-widest shadow-lg shadow-amber-200 animate-pulse border-2 border-white">Menunggu Verifikasi</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="mt-4 text-[10px] text-amber-700/60 font-bold italic text-center uppercase tracking-widest">Data di atas adalah riwayat transaksi yang Anda ajukan.</p>
    </div>
    <?php endif; ?>

    <!-- Form Upload DU -->
    <div class="bg-white border rounded-lg shadow-sm mb-8 overflow-hidden" 
         x-data="{ nominal_du: '', nominal_infaq: '' }"
         x-init="$watch('nominal_du', v => { nominal_du = formatRupiah(v) }); $watch('nominal_infaq', v => { nominal_infaq = formatRupiah(v) })">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-800">📤 Upload Pembayaran Baru</h3>
            <p class="text-sm text-gray-500 mt-1">Pembayaran ditransfer ke rekening BSI: <b><?= htmlspecialchars($settings['no_rekening_bsi'] ?? '') ?></b> (A.N. <?= htmlspecialchars($settings['nama_rekening_bsi'] ?? '') ?>)</p>
        </div>
        <div class="p-6">
            <form action="<?= url('santri/upload-pembayaran-du') ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">a. Biaya Daftar Ulang (Rp)</label>
                        <input type="text" name="nominal_bayar" required x-model="nominal_du" placeholder="Contoh: 1.000.000" class="w-full border-gray-300 rounded-2xl shadow-sm focus:ring-emerald-500 focus:border-emerald-500 px-4 py-3 border font-bold text-lg text-emerald-700">
                    </div>
 
                    <div class="bg-emerald-50 rounded-2xl border border-emerald-200 p-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                            <span class="mr-2">b. Infaq / Shodaqoh (Rp)</span> <span class="text-xs text-emerald-600 bg-emerald-100 px-2 py-0.5 rounded">* opsional/sukarela</span>
                        </label>
                        <div class="text-sm text-gray-600 mb-3 italic">
                            "<?= nl2br(htmlspecialchars($settings['pesan_infaq'] ?? '')) ?>"
                        </div>
                        <input type="text" name="nominal_infaq" x-model="nominal_infaq" placeholder="Contoh: 100.000" class="w-full border-gray-300 rounded-2xl shadow-sm focus:ring-emerald-500 focus:border-emerald-500 px-4 py-3 border font-medium text-emerald-600">
                    </div>
 
                    <div class="border-t border-gray-200 pt-4">
                        <div class="flex flex-col sm:flex-row justify-between items-center bg-gray-50 p-4 sm:p-6 rounded-2xl font-bold gap-2">
                            <span class="text-gray-700 uppercase tracking-wider text-xs">Total Transfer:</span>
                            <span class="text-xl sm:text-3xl text-emerald-600 text-center sm:text-right">
                                Rp <span x-text="new Intl.NumberFormat('id-ID').format(parseRupiah(nominal_du) + parseRupiah(nominal_infaq))">0</span>
                            </span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bukti Transfer / Kwitansi</label>
                        <input type="file" name="bukti_transfer" accept="image/*,.pdf" required class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                        <textarea name="catatan_santri" rows="2" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 px-3 py-2 border"></textarea>
                    </div>

                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-4 rounded-lg shadow transition">
                        📤 Upload & Kirim
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Hubungi Bendahara -->
    <div class="bg-gray-50 border rounded-lg p-6 flex flex-col sm:flex-row items-center justify-between shadow-sm">
        <div class="mb-4 sm:mb-0">
            <h4 class="font-bold text-gray-800 flex items-center">
                <svg class="h-5 w-5 mr-2 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                Butuh Bantuan Seputar Pembayaran?
            </h4>
            <p class="text-sm text-gray-600 mt-1">Silakan hubungi Bendahara Daftar Ulang untuk konsultasi atau kendala pembayaran.</p>
        </div>
        <div class="flex flex-col items-center sm:items-end">
            <p class="font-bold text-gray-900"><?= htmlspecialchars($bendahara_du_info['name'] ?? $settings['nama_bendahara_du'] ?? 'Bendahara') ?></p>
            <?php 
            $wa_du = $bendahara_du_info['no_wa'] ?? $settings['wa_bendahara_du'] ?? '';
            $wa_du_clean = preg_replace('/[^0-9]/', '', $wa_du);
            ?>
            <a href="https://wa.me/<?= $wa_du_clean ?>" target="_blank" class="mt-1 inline-flex items-center px-3 py-1 bg-emerald-50 text-emerald-600 hover:bg-emerald-100 rounded-full text-xs font-bold transition-all border border-emerald-100">
                <svg class="mr-1.5 w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M20.52 3.44A12.028 12.028 0 0012 0C5.38 0 0 5.38 0 12c0 2.12.55 4.16 1.6 5.96L0 24l6.14-1.61A12.067 12.067 0 0012 24c6.62 0 12-5.38 12-12 0-3.21-1.25-6.22-3.48-8.56zM12 21.98c-1.78 0-3.52-.48-5.06-1.38l-.36-.21-3.76.99 1-3.66-.23-.37A9.972 9.972 0 012.02 12c0-5.51 4.49-10 10-10 2.67 0 5.18 1.04 7.07 2.93a9.96 9.96 0 012.91 7.07c0 5.51-4.49 10-9.98 10zm5.49-7.5c-.3-.15-1.78-.88-2.06-.98-.28-.1-.48-.15-.68.15-.2.3-.78.98-.95 1.18-.18.2-.35.23-.65.08-.3-.15-1.27-.47-2.42-1.49-.89-.79-1.49-1.77-1.67-2.07-.18-.3-.02-.46.13-.61.13-.14.3-.35.45-.53.15-.18.2-.3.3-.5.1-.2.05-.38-.02-.53-.08-.15-.68-1.64-.93-2.25-.24-.6-.48-.52-.68-.53h-.58c-.2 0-.53.08-.8.38-.28.3-1.05 1.03-1.05 2.51s1.08 2.91 1.23 3.11c.15.2 2.12 3.24 5.13 4.54 2.16.94 2.87.98 3.96.83 1.21-.17 2.65-1.08 3.03-2.12.38-1.04.38-1.93.26-2.12-.11-.2-.41-.3-.71-.45z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
                Hubungi WhatsApp
            </a>
        </div>
    </div>

</div>
