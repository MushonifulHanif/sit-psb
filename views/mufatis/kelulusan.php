<?php ob_start(); ?>
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Keputusan Kelulusan</h1>
        <p class="text-gray-600 mt-1">Tentukan kelulusan santri berdasarkan nilai PG dan nilai Lisan.</p>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Santri</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">Nilai CBT (PG)</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">Nilai Lisan</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">Rata-Rata</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Saat Ini</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keputusan</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($santri as $row): 
                    $avg = ($row['skor_pg'] + ($row['nilai_lisan'] ?: 0)) / 2;
                ?>
                <tr class="<?= $row['status_psb'] == 'lulus' ? 'bg-green-50' : ($row['status_psb'] == 'gagal' ? 'bg-red-50' : '') ?>">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($row['name']) ?></div>
                        <div class="text-xs text-gray-500"><?= htmlspecialchars($row['username']) ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                        <span class="inline-block bg-gray-100 text-gray-800 px-3 py-1 rounded font-bold"><?= $row['skor_pg'] ?></span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                        <?php if($row['nilai_lisan'] !== null): ?>
                            <span class="inline-block bg-emerald-100 text-emerald-800 px-3 py-1 rounded font-bold"><?= $row['nilai_lisan'] ?></span>
                        <?php else: ?>
                            <span class="text-gray-400 italic">0</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                        <span class="text-lg font-black <?= $avg >= 70 ? 'text-green-600' : 'text-red-500' ?>"><?= number_format($avg, 1) ?></span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?php if($row['status_kelulusan'] == 'lulus'): ?>
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-green-200 text-green-800 uppercase">LULUS</span>
                        <?php elseif($row['status_kelulusan'] == 'gagal'): ?>
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-red-200 text-red-800 uppercase">TIDAK LULUS</span>
                        <?php else: ?>
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-gray-200 text-gray-800 uppercase">BELUM DIPUTUSKAN</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <?php if($row['status_psb'] == 'sudah_ujian' || $row['status_psb'] == 'lulus' || $row['status_psb'] == 'gagal'): ?>
                            <form action="<?= url('mufatis/proses-kelulusan') ?>" method="POST" class="flex flex-col sm:flex-row gap-2 justify-center">
                                <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                                <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                                
                                <button type="button" @click="document.getElementById('status_<?= $row['id'] ?>').value='lulus'; confirmSubmit($el.form, 'Nyatakan santri LULUS?')" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-2xl shadow text-xs uppercase font-black tracking-widest transition-all">
                                    LULUS
                                </button>
                                <button type="button" @click="document.getElementById('status_<?= $row['id'] ?>').value='gagal'; confirmSubmit($el.form, 'Nyatakan santri GAGAL?')" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-2xl shadow text-xs uppercase font-black tracking-widest transition-all">
                                    GAGAL
                                </button>
                                <input type="hidden" name="status" id="status_<?= $row['id'] ?>" value="">
                            </form>
                        <?php else: ?>
                            <span class="text-xs text-gray-500 italic">Sudah berstatus: <?= $row['status_psb'] ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if(empty($santri)): ?>
                <tr><td colspan="6" class="px-6 py-10 text-center text-gray-500">Belum ada santri siap ditentukan kelulusannya.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = "Keputusan Kelulusan - SIT-PSB";
require __DIR__ . '/../layouts/admin.php';
