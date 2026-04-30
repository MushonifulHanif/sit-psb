<?php ob_start(); ?>
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Manajemen Gelombang</h1>
        <p class="text-gray-600 mt-1">Atur jadwal pendaftaran dan ujian per gelombang.</p>
    </div>
    <button onclick="document.getElementById('modalAdd').classList.remove('hidden')" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded shadow flex items-center">
        + Gelombang Baru
    </button>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Gelombang</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status Pendaftaran</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jadwal Seleksi</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($gelombang as $row): 
                    $now = date('Y-m-d');
                    $isBuka = $row['is_active'] && ($now >= $row['tgl_buka'] && $now <= $row['tgl_tutup']);
                ?>
                <tr class="<?= $row['is_active'] ? 'bg-emerald-50' : '' ?>">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-bold text-gray-900"><?= htmlspecialchars($row['nama']) ?></div>
                        <div class="text-xs text-gray-500">
                            Aktif: <span class="<?= $row['is_active'] ? 'text-emerald-600 font-bold' : 'text-gray-400' ?>"><?= $row['is_active'] ? 'YA (DEFAULT)' : 'TIDAK' ?></span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                            <?= format_indo_date($row['tgl_buka']) ?> - <?= format_indo_date($row['tgl_tutup']) ?>
                        </div>
                        <span class="inline-flex mt-1 text-xs leading-4 font-semibold rounded-full px-2 <?= $isBuka ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                            <?= $isBuka ? 'Pendaftaran Dibuka' : 'Ditutup / Menunggu' ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <?= format_indo_date($row['jadwal_ujian_mulai']) ?> 
                        s/d 
                        <?= format_indo_date($row['jadwal_ujian_selesai']) ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <?php if(!$row['is_active']): ?>
                            <a href="<?= url("admin/toggle-gelombang/{$row['id']}") ?>" class="text-indigo-600 hover:text-indigo-900 mr-3">Jadikan Aktif</a>
                        <?php endif; ?>
                        <a href="<?= url("admin/delete-gelombang/{$row['id']}") ?>" class="text-red-600 hover:text-red-900" onclick="return confirmLink(event, 'Hapus gelombang secara permanen?')">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Add Gelombang -->
<div id="modalAdd" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/20 backdrop-blur-sm">
    <div class="bg-white/70 backdrop-blur-xl border border-white/40 shadow-2xl rounded-[2.5rem] max-w-lg w-full overflow-hidden">
        <form action="<?= url('admin/store-gelombang') ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
            <div class="px-8 py-6 border-b border-gray-100">
                <h3 class="text-xl font-black text-gray-900">Setup Gelombang Pendaftaran</h3>
            </div>
            <div class="p-8 space-y-6">
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-emerald-700 mb-2 ml-1">Nama Gelombang</label>
                    <input type="text" name="nama" required placeholder="Contoh: Gelombang 1 - 2026" class="w-full border-gray-200 rounded-2xl shadow-sm focus:ring-emerald-500/20 focus:border-emerald-500 px-4 py-3.5 border bg-white/50 hover:bg-white transition-all text-sm font-medium">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-emerald-700 mb-2 ml-1">Tgl Buka</label>
                        <input type="text" name="tgl_buka" required class="datepicker w-full border-gray-200 rounded-2xl shadow-sm focus:ring-emerald-500/20 focus:border-emerald-500 px-4 py-3.5 border bg-white/50 text-sm font-medium">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-emerald-700 mb-2 ml-1">Tgl Tutup</label>
                        <input type="text" name="tgl_tutup" required class="datepicker w-full border-gray-200 rounded-2xl shadow-sm focus:ring-emerald-500/20 focus:border-emerald-500 px-4 py-3.5 border bg-white/50 text-sm font-medium">
                    </div>
                </div>
                <div class="p-5 bg-emerald-50/50 border border-emerald-100/50 rounded-3xl">
                    <p class="text-[10px] text-emerald-800 font-black mb-4 uppercase tracking-widest flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        Periode Ujian Seleksi / CBT
                    </p>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[9px] font-bold text-gray-500 mb-1 uppercase tracking-wider">Dari Tanggal</label>
                            <input type="text" name="jadwal_ujian_mulai" required class="datepicker w-full border-gray-200 rounded-xl shadow-sm focus:ring-emerald-500/20 focus:border-emerald-500 px-4 py-2.5 border bg-white/50 text-xs font-bold">
                        </div>
                        <div>
                            <label class="block text-[9px] font-bold text-gray-500 mb-1 uppercase tracking-wider">Sampai Tanggal</label>
                            <input type="text" name="jadwal_ujian_selesai" required class="datepicker w-full border-gray-200 rounded-xl shadow-sm focus:ring-emerald-500/20 focus:border-emerald-500 px-4 py-2.5 border bg-white/50 text-xs font-bold">
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50/50 px-8 py-6 flex justify-end space-x-3 border-t border-gray-100">
                <button type="button" onclick="document.getElementById('modalAdd').classList.add('hidden')" class="px-6 py-2.5 text-xs font-black uppercase tracking-widest text-gray-400 hover:text-gray-600 transition-all">Batal</button>
                <button type="submit" class="px-8 py-2.5 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 shadow-lg shadow-emerald-100 font-black text-xs uppercase tracking-widest transition-all transform active:scale-95">Simpan Gelombang</button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = "Manajemen Gelombang - SIT-PSB";
require __DIR__ . '/../layouts/admin.php';
