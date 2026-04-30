<?php ob_start(); ?>
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Verifikasi Daftar Ulang</h1>
    <p class="text-gray-600 mt-1">Daftar transfer pembayaran cicilan daftar ulang dan infaq dari santri yang lulus.</p>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Santri</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nominal DU</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Infaq</th>
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
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                        Rp <?= number_format($row['nominal_bayar'], 0, ',', '.') ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-emerald-600">
                        Rp <?= number_format($row['nominal_infaq'], 0, ',', '.') ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600">
                        <a href="<?= asset('../uploads/bukti_bayar/' . $row['bukti_transfer']) ?>" @click.prevent="$dispatch('image-preview', { src: $el.href, title: 'Bukti Transfer DU' })" class="hover:underline flex items-center cursor-pointer">
                            <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            Bukti
                        </a>
                        <?php if($row['catatan_santri']): ?>
                            <div class="mt-1 text-xs text-gray-500 italic max-w-xs truncate" title="<?= htmlspecialchars($row['catatan_santri']) ?>">&quot;<?= htmlspecialchars($row['catatan_santri']) ?>&quot;</div>
                        <?php endif; ?>
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
                        <?php if($row['status'] == 'pending'): ?>
                            <button @click="openModal = true" class="text-indigo-600 hover:text-indigo-900 border border-indigo-200 bg-indigo-50 px-3 py-1 rounded">Verifikasi</button>
                            
                            <!-- Verifikasi Modal -->
                            <div x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm" x-cloak>
                                <div class="bg-white rounded-[2rem] shadow-2xl max-w-md w-full relative" @click.away="openModal = false">
                                    <form action="<?= url("bendahara-du/do-verifikasi/{$row['id']}") ?>" method="POST">
                                        <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                                        <div class="px-8 py-5 border-b border-gray-100">
                                            <h3 class="text-xl font-black text-gray-900">Verifikasi Pembayaran DU</h3>
                                        </div>
                                        <div class="p-8">
                                            <div class="mb-6 bg-gray-50/50 p-4 rounded-2xl border border-gray-100 text-sm space-y-1">
                                                <p>Santri: <span class="font-black text-gray-900"><?= htmlspecialchars($row['name']) ?></span></p>
                                                <p>Bayar DU: <span class="font-bold text-emerald-600">Rp <?= number_format($row['nominal_bayar'], 0, ',', '.') ?></span></p>
                                                <p>Infaq: <span class="font-bold text-emerald-600">Rp <?= number_format($row['nominal_infaq'], 0, ',', '.') ?></span></p>
                                            </div>
                                            
                                            <div class="mb-4">
                                                <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wider text-[10px]">Status Verifikasi</label>
                                                <div class="relative group" x-data="{ 
                                                    open: false, 
                                                    status: 'diterima',
                                                    options: [
                                                        { label: '✅ Terima (Masuk Saldo)', value: 'diterima' },
                                                        { label: '❌ Tolak', value: 'ditolak' }
                                                    ],
                                                    get selectedLabel() {
                                                        return this.options.find(o => o.value == this.status)?.label || 'Pilih Status'
                                                    }
                                                }">
                                                    <input type="hidden" name="status" :value="status" required>
                                                    <button type="button" @click="open = !open" @click.away="open = false"
                                                            class="w-full flex items-center justify-between px-4 py-3 border border-gray-200 rounded-xl text-sm transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-gray-50/50 hover:bg-white font-bold">
                                                        <span x-text="selectedLabel" :class="status == 'diterima' ? 'text-green-700' : 'text-red-700'"></span>
                                                        <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                                    </button>

                                                    <div x-show="open" x-transition x-cloak
                                                         class="absolute z-[70] left-0 right-0 mt-2 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden p-2">
                                                        <template x-for="item in options" :key="item.value">
                                                            <div @click="status = item.value; open = false" 
                                                                 class="flex items-center justify-between px-5 py-3 rounded-xl cursor-pointer hover:bg-gray-50 transition-all"
                                                                 :class="status == item.value ? 'bg-gray-50' : ''">
                                                                <span x-text="item.label" class="text-xs font-bold whitespace-nowrap" :class="status == item.value ? (item.value == 'diterima' ? 'text-green-700' : 'text-red-700') : 'text-gray-600'"></span>
                                                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all ml-4"
                                                                     :class="status == item.value ? 'border-indigo-500 bg-indigo-500' : 'border-gray-200 bg-white'">
                                                                    <div x-show="status == item.value" x-transition class="w-1.5 h-1.5 bg-white rounded-full"></div>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>
                                                <p class="text-[10px] text-gray-500 mt-2 italic px-1">Menerima pembayaran ini juga akan membuka akses E-Seragam untuk santri.</p>
                                            </div>
                                            
                                            <div>
                                                <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wider text-[10px]">Catatan Verifikasi</label>
                                                <textarea name="catatan" rows="2" class="w-full border-gray-200 rounded-2xl shadow-sm focus:ring-indigo-500/20 focus:border-indigo-500 p-4 border bg-gray-50/50 hover:bg-white transition-all text-sm"></textarea>
                                            </div>
                                        </div>
                                        <div class="bg-gray-50 px-8 py-5 flex justify-end space-x-3 rounded-b-[2rem] border-t border-gray-100">
                                            <button type="button" @click="openModal = false" class="px-6 py-2.5 text-sm font-bold text-gray-400 hover:text-gray-600 transition-colors uppercase tracking-widest">Batal</button>
                                            <button type="submit" class="px-8 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 font-bold shadow-lg shadow-indigo-100 transition-all active:scale-95">Simpan Verifikasi</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php else: ?>
                            <span class="text-gray-400">Selesai</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if (empty($pembayaran)): ?>
                <tr><td colspan="7" class="px-6 py-10 text-center text-gray-500">Belum ada pembayaran daftar ulang masuk.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = "Verifikasi Daftar Ulang - SIT-PSB";
require __DIR__ . '/../layouts/admin.php';
