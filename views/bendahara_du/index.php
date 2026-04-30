<?php ob_start(); ?>
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Tracking Piutang Daftar Ulang</h1>
        <p class="text-gray-600 mt-1">Status pelunasan pembayaran santri yang LULUS.</p>
    </div>
    <div class="bg-indigo-100 text-indigo-800 px-4 py-2 rounded-lg font-semibold">
        Biaya DU: Rp <?= number_format($biaya_du, 0, ',', '.') ?>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Santri</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status PSB</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Dibayar</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Infaq (Ekstra)</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kekurangan</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Bayar</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($santri as $row): 
                    $kurang = max(0, $biaya_du - $row['total_dibayar']);
                    $persen = $biaya_du > 0 ? min(100, ($row['total_dibayar'] / $biaya_du) * 100) : 100;
                ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($row['name']) ?></div>
                        <div class="text-xs text-gray-500"><?= htmlspecialchars($row['username']) ?></div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 uppercase font-semibold">
                        <?= str_replace('_', ' ', $row['status_psb']) ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 font-bold">
                        Rp <?= number_format($row['total_dibayar'], 0, ',', '.') ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-emerald-500">
                        Rp <?= number_format($row['total_infaq'], 0, ',', '.') ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-500 font-bold">
                        Rp <?= number_format($kurang, 0, ',', '.') ?>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-16 bg-gray-200 rounded-full h-2.5 mr-2">
                              <div class="bg-indigo-600 h-2.5 rounded-full" style="width: <?= $persen ?>%"></div>
                            </div>
                            <span class="text-xs font-semibold text-gray-700"><?= round($persen) ?>%</span>
                        </div>
                        <?php if($kurang == 0): ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 mt-1">LUNAS</span>
                        <?php else: ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 mt-1">BELUM LUNAS</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if(empty($santri)): ?>
                <tr><td colspan="6" class="px-6 py-10 text-center text-gray-500">Belum ada santri Lulus/Daftar Ulang.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = "Bendahara Daftar Ulang - SIT-PSB";
require __DIR__ . '/../layouts/admin.php';
