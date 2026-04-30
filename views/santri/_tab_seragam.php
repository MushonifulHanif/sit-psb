<?php if ($seragam_sudah): ?>
    <?php $ukuran = json_decode($seragam_sudah['detail_ukuran_json'], true); ?>
    <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-xl overflow-hidden max-w-4xl mx-auto">
        <div class="bg-emerald-600 px-8 py-6">
            <h3 class="text-xl font-black text-white flex items-center uppercase tracking-widest">
                <div class="bg-white/20 p-2 rounded-xl mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                Data Ukuran Seragam Tersimpan
            </h3>
        </div>
        <div class="p-8">
            <p class="text-gray-500 mb-8 font-medium italic">Anda telah mengirimkan data ukuran pakaian. Pakaian Anda akan diproses sesuai dengan ukuran di bawah ini.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php foreach($item_seragam_list as $item): ?>
                    <div class="border border-gray-100 rounded-[2rem] px-6 py-5 bg-gray-50/50 flex justify-between items-center group hover:bg-white hover:shadow-md transition-all">
                        <div>
                            <p class="text-[10px] font-black text-emerald-800 uppercase tracking-widest mb-1"><?= htmlspecialchars($item['nama_item']) ?></p>
                            <?php if($item['keterangan']): ?>
                                <p class="text-[10px] text-gray-400 font-medium"><?= htmlspecialchars($item['keterangan']) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="font-black text-xl text-emerald-600 bg-white px-4 py-2 rounded-2xl shadow-inner border border-emerald-50">
                            <?= htmlspecialchars($ukuran[$item['id']] ?? '-') ?> <span class="text-xs text-gray-400"><?= htmlspecialchars($item['satuan']) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <?php if(!empty($seragam_sudah['catatan'])): ?>
            <div class="mt-8 bg-amber-50 p-6 rounded-[2rem] border border-amber-100">
                <p class="text-[10px] font-black text-amber-800 uppercase tracking-widest mb-3 px-1">Catatan Tambahan:</p>
                <p class="text-gray-700 italic font-medium px-1">"<?= nl2br(htmlspecialchars($seragam_sudah['catatan'])) ?>"</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <?php if ($status != 'selesai'): ?>
    <div class="max-w-3xl mx-auto">
        <div class="bg-indigo-50 border-l-4 border-indigo-500 p-6 rounded-r-3xl mb-8 shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-indigo-100 p-2 rounded-xl">
                    <svg class="h-6 w-6 text-indigo-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" /></svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-bold text-indigo-900 leading-relaxed">
                        Mohon ukur dengan seksama, apabila kesulitan anda dapat berkonsultasi dengan penjahit.
                    </p>
                </div>
            </div>
        </div>

        <form action="<?= url('santri/submit-seragam') ?>" method="POST" onsubmit="return confirmSubmit(this, 'Apakah ukuran seragam sudah sesuai?', 'Setelah dikirim data ini tidak dapat diubah')" class="bg-white border border-gray-100 rounded-[2.5rem] shadow-xl p-8 sm:p-10 overflow-hidden">
             <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
             
             <h3 class="text-2xl font-black text-gray-900 mb-8 uppercase tracking-tighter">Form Ukuran Seragam</h3>
             
             <div class="space-y-6">
                  <?php foreach($item_seragam_list as $item): ?>
                  <div class="flex flex-col sm:flex-row sm:items-center justify-between p-5 border border-gray-100 rounded-3xl hover:bg-emerald-50/30 transition-all group">
                      <div class="mb-3 sm:mb-0 sm:w-1/2">
                          <label for="item_<?= $item['id'] ?>" class="block text-[11px] font-black uppercase tracking-widest text-emerald-800 mb-1 ml-1"><?= htmlspecialchars($item['nama_item']) ?> <span class="text-red-500">*</span></label>
                          <?php if($item['keterangan']): ?>
                             <p class="text-xs text-gray-500 font-medium ml-1"><?= htmlspecialchars($item['keterangan']) ?></p>
                          <?php endif; ?>
                      </div>
                      <div class="sm:w-1/3">
                          <div class="relative group">
                              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-emerald-500 transition-colors">
                                  <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 13h1m1 0h1m1 0h1m1 0h1m1 0h1m1 0h1m1 0h1m1 0h1m1 0h1m1 0h1m1 0h1m1 0h1m1 0h1m1 0h1m1 0h1m1 0h1m1 0h1M11 13h1" /></svg>
                              </div>
                              <input type="number" id="item_<?= $item['id'] ?>" name="ukuran[<?= $item['id'] ?>]" required min="1" step="0.1" class="w-full pl-11 pr-12 rounded-2xl border-gray-200 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 py-3.5 shadow-sm text-sm font-bold placeholder-gray-300" placeholder="0">
                              <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-400 font-bold text-xs">
                                  <?= htmlspecialchars($item['satuan']) ?>
                              </div>
                          </div>
                      </div>
                  </div>
                  <?php endforeach; ?>
                  
                  <div class="pt-6">
                      <label for="catatan_seragam" class="block text-[10px] font-black uppercase tracking-widest text-emerald-700 mb-2 ml-1">Catatan Tambahan (Opsional)</label>
                      <div class="relative group">
                          <div class="absolute top-4 left-4 pointer-events-none text-gray-400 group-focus-within:text-emerald-500 transition-colors">
                              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                          </div>
                          <textarea id="catatan_seragam" name="catatan" rows="2" class="w-full pl-11 pr-4 rounded-2xl border-gray-200 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 py-3.5 shadow-sm text-sm font-medium" placeholder="Misal: Lengan minta dipanjangkan sedikit..."></textarea>
                      </div>
                  </div>
                  
                  <div class="pt-6">
                      <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-black py-5 rounded-3xl shadow-2xl shadow-emerald-200 transition-all transform active:scale-95 uppercase tracking-widest text-sm flex items-center justify-center">
                          <svg class="w-6 h-6 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                          Simpan Data Ukuran Seragam
                      </button>
                  </div>
             </div>
        </form>
    </div>
    <?php endif; ?>
<?php endif; ?>
