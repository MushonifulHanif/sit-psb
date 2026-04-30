<?php ob_start(); ?>
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Penilaian Ujian Lisan & Hafalan</h1>
        <p class="text-gray-600 mt-1">Daftar rekaman ujian santri dan form input nilai.</p>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Santri</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai CBT (PG)</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rekaman Suara</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai Lisan</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($santri as $row): 
                    $rekaman = json_decode($row['rekaman_json'], true) ?: [];
                ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($row['name']) ?></div>
                        <div class="text-xs text-gray-500"><?= htmlspecialchars($row['username']) ?></div>
                        <span class="inline-flex mt-1 text-xs px-2 rounded-full border">Status: <?= htmlspecialchars($row['status_psb']) ?></span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                        <span class="inline-block bg-gray-100 text-gray-800 px-3 py-1 rounded font-bold"><?= $row['skor_pg'] ?></span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        <?php if (empty($rekaman)): ?>
                            <span class="text-gray-400 italic">Tidak ada rekaman audio</span>
                        <?php else: ?>
                            <div class="space-y-2">
                                <?php foreach($rekaman as $soal_id => $file): ?>
                                    <div class="flex items-center text-xs">
                                        <span class="w-16 text-gray-500">Soal #<?= $soal_id ?>:</span>
                                        <audio controls src="<?= url('uploads/rekaman/' . $file) ?>" class="h-8 w-48"></audio>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="mt-2 text-xs italic border-l-2 pl-2 border-emerald-400 text-emerald-700">
                           + Video hafalan dikirim manual via WhatsApp
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                        <?php if($row['nilai'] !== null): ?>
                            <span class="inline-block bg-emerald-100 text-emerald-800 px-3 py-1 rounded font-bold"><?= $row['nilai'] ?></span>
                        <?php else: ?>
                            <span class="text-gray-400 italic">Belum dinilai</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" x-data="{ openModal: false }">
                        <button @click="openModal = true" class="text-indigo-600 hover:text-indigo-900 border border-indigo-200 bg-indigo-50 px-3 py-1 rounded">Input Nilai</button>
                        
                        <!-- Modal Input Nilai -->
                        <div x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/20 backdrop-blur-sm" x-cloak>
                            <div class="bg-white/70 backdrop-blur-xl border border-white/40 shadow-2xl rounded-[2.5rem] max-w-md w-full relative whitespace-normal" @click.away="openModal = false">
                                <form action="<?= url("mufatis/simpan-nilai/{$row['id']}") ?>" method="POST">
                                    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                                    <div class="px-8 py-6 border-b border-gray-100">
                                        <h3 class="text-xl font-black text-gray-900">Input Nilai Seleksi</h3>
                                    </div>
                                    <div class="p-8">
                                        <div class="mb-6 bg-indigo-50/50 p-5 rounded-3xl border border-indigo-100/50">
                                            <p class="text-[10px] font-black uppercase tracking-widest text-indigo-400 mb-2">Penilaian Santri</p>
                                            <p class="text-sm font-black text-gray-900"><?= htmlspecialchars($row['name']) ?></p>
                                            <p class="text-[11px] text-indigo-700 font-medium mt-2 leading-relaxed break-words">Gabungkan evaluasi dari rekaman audio CBT dan video hafalan yang dikirim via WhatsApp.</p>
                                        </div>
                                        
                                        <div class="mb-6">
                                            <label class="block text-[10px] font-black uppercase tracking-widest text-indigo-800 mb-2 ml-1">Skor Lisan (0-100)</label>
                                            <input type="number" name="nilai" required min="0" max="100" value="<?= $row['nilai'] ?? '' ?>" class="w-full border-gray-200 rounded-2xl shadow-sm focus:ring-indigo-500/20 focus:border-indigo-500 px-5 py-4 border bg-white/50 text-lg font-black text-indigo-700" placeholder="0">
                                        </div>
                                        
                                        <div>
                                            <label class="block text-[10px] font-black uppercase tracking-widest text-indigo-800 mb-2 ml-1">Catatan Evaluasi</label>
                                            <textarea name="catatan" rows="3" class="w-full border-gray-200 rounded-2xl shadow-sm focus:ring-indigo-500/20 focus:border-indigo-500 px-5 py-4 border bg-white/50 text-sm font-medium" placeholder="Kelancaran, makhroj, tajwid..."><?= htmlspecialchars($row['lisan_catatan'] ?? '') ?></textarea>
                                        </div>
                                    </div>
                                    <div class="bg-gray-50/50 px-8 py-6 flex justify-end space-x-3 border-t border-gray-100 rounded-b-[2.5rem]">
                                        <button type="button" @click="openModal = false" class="px-6 py-2.5 text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-gray-600 transition-colors">Batal</button>
                                        <button type="submit" class="px-8 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 font-black text-[11px] uppercase tracking-widest shadow-lg shadow-indigo-100 transition-all active:scale-95">Simpan Nilai</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if(empty($santri)): ?>
                <tr><td colspan="5" class="px-6 py-10 text-center text-gray-500">Belum ada santri yang menyelesaikan ujian CBT.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = "Penilaian Mufatis - SIT-PSB";
require __DIR__ . '/../layouts/admin.php';
