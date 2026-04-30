<?php ob_start(); ?>
<div x-data="{ 
    showAdd: false, 
    showEdit: false, 
    addOpen: false,
    editOpen: false,
    addJk: 'semua',
    editItem: { id: '', nama_item: '', jk: 'semua', satuan: 'cm', urutan: 0 },
    genderOptions: [
        { label: 'Semua (Putra & Putri)', value: 'semua' },
        { label: 'Laki-laki (Putra) Saja', value: 'L' },
        { label: 'Perempuan (Putri) Saja', value: 'P' }
    ],
    get addGenderLabel() {
        return this.genderOptions.find(o => o.value == this.addJk)?.label || 'Pilih Peruntukan'
    },
    get editGenderLabel() {
        return this.genderOptions.find(o => o.value == this.editItem.jk)?.label || 'Pilih Peruntukan'
    },
    openEdit(item) {
        this.editItem = { ...item };
        this.showEdit = true;
        this.editOpen = false;
    }
}">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Item Seragam</h1>
            <p class="text-gray-600 mt-1">Kelola item seragam dan ukuran yang tersedia.</p>
        </div>
        <button @click="showAdd = true; addOpen = false" class="bg-emerald-600 hover:bg-emerald-700 text-white font-black py-2.5 px-6 rounded-2xl shadow-lg shadow-emerald-100 flex items-center transition-all active:scale-95 uppercase text-xs tracking-widest">
            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            Tambah Item
        </button>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest w-16">Urutan</th>
                        <th scope="col" class="px-6 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Item / Nama</th>
                        <th scope="col" class="px-6 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Peruntukan</th>
                        <th scope="col" class="px-6 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Satuan</th>
                        <th scope="col" class="px-6 py-3 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($item_seragam as $row): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-black text-emerald-600">
                            <?= str_pad($row['urutan'], 2, '0', STR_PAD_LEFT) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-gray-900"><?= htmlspecialchars($row['nama_item']) ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-1 inline-flex text-[10px] font-black leading-5 rounded-full bg-blue-100 text-blue-800 uppercase tracking-widest">
                                <?= $row['jk'] == 'semua' ? 'Semua' : ($row['jk'] == 'L' ? 'Putra' : 'Putri') ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-bold uppercase">
                            <?= htmlspecialchars($row['satuan']) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                            <button @click="openEdit(<?= htmlspecialchars(json_encode($row)) ?>)" class="text-emerald-600 hover:text-emerald-900 font-black uppercase text-xs tracking-widest">Edit</button>
                            <a href="<?= url("admin/delete-item-seragam/{$row['id']}") ?>" class="text-red-600 hover:text-red-900 font-black uppercase text-xs tracking-widest" onclick="return confirmLink(event, 'Hapus item seragam?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($item_seragam)): ?>
                        <tr><td colspan="5" class="px-6 py-10 text-center text-gray-500">Belum ada item seragam ditambahkan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Add Item -->
    <div x-show="showAdd" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/20 backdrop-blur-sm" x-cloak>
        <div class="bg-white/70 backdrop-blur-xl border border-white/40 shadow-2xl rounded-[2.5rem] max-w-md w-full relative whitespace-normal" @click.away="showAdd = false">
            <form action="<?= url('admin/store-item-seragam') ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                <div class="px-8 py-6 border-b border-gray-100 rounded-t-[2.5rem]">
                    <h3 class="text-xl font-black text-gray-900">Tambah Item Seragam</h3>
                </div>
                <div class="p-8 space-y-6">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-emerald-700 mb-2 ml-1">Nama Item</label>
                        <input type="text" name="nama_item" required placeholder="Contoh: Lingkar Dada" class="w-full border-gray-200 rounded-2xl shadow-sm focus:ring-emerald-500/20 focus:border-emerald-500 px-5 py-4 border bg-white/50 text-sm font-bold">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-emerald-700 mb-2 ml-1">Peruntukan Gender</label>
                        <div class="relative group">
                            <input type="hidden" name="jk" :value="addJk" required>
                            <button type="button" @click="addOpen = !addOpen" @click.away="addOpen = false"
                                    class="w-full flex items-center justify-between px-5 py-4 border border-gray-200 rounded-2xl text-sm transition-all focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 bg-white/50 hover:bg-white font-black text-emerald-800">
                                <span x-text="addGenderLabel"></span>
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="addOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </button>

                            <div x-show="addOpen" x-transition x-cloak
                                 class="absolute z-[70] left-0 right-0 mt-2 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden p-2">
                                <template x-for="opt in genderOptions" :key="opt.value">
                                    <div @click="addJk = opt.value; addOpen = false" 
                                         class="flex items-center justify-between px-5 py-3 rounded-xl cursor-pointer hover:bg-emerald-50 transition-all font-black"
                                         :class="addJk == opt.value ? 'bg-emerald-50 text-emerald-700' : 'text-gray-600'">
                                        <span x-text="opt.label" class="text-[10px] uppercase tracking-widest whitespace-nowrap"></span>
                                        <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all ml-4"
                                             :class="addJk == opt.value ? 'border-emerald-500 bg-emerald-500' : 'border-gray-200 bg-white'">
                                            <div x-show="addJk == opt.value" x-transition class="w-1.5 h-1.5 bg-white rounded-full"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-emerald-700 mb-2 ml-1">Satuan</label>
                            <input type="text" name="satuan" value="cm" required class="w-full border-gray-200 rounded-2xl shadow-sm focus:ring-emerald-500/20 focus:border-emerald-500 px-4 py-3 border bg-white/50 text-sm font-bold">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-emerald-700 mb-2 ml-1">Urutan</label>
                            <input type="number" name="urutan" value="0" class="w-full border-gray-200 rounded-2xl shadow-sm focus:ring-emerald-500/20 focus:border-emerald-500 px-4 py-3 border bg-white/50 text-sm font-bold">
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50/50 px-8 py-6 flex justify-end space-x-3 border-t border-gray-100 rounded-b-[2.5rem]">
                    <button type="button" @click="showAdd = false" class="px-6 py-2.5 text-xs font-black uppercase tracking-widest text-gray-400 hover:text-gray-600 transition-all">Batal</button>
                    <button type="submit" class="px-8 py-2.5 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 font-black text-xs uppercase tracking-widest shadow-lg shadow-emerald-100 transition-all active:scale-95">Simpan Item</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Item -->
    <div x-show="showEdit" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/20 backdrop-blur-sm" x-cloak>
        <div class="bg-white/70 backdrop-blur-xl border border-white/40 shadow-2xl rounded-[2.5rem] max-w-md w-full relative whitespace-normal" @click.away="showEdit = false">
            <form :action="'<?= url('admin/update-item-seragam/') ?>' + editItem.id" method="POST">
                <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                <div class="px-8 py-6 border-b border-gray-100 rounded-t-[2.5rem]">
                    <h3 class="text-xl font-black text-gray-900">Edit Item Seragam</h3>
                </div>
                <div class="p-8 space-y-6">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-emerald-700 mb-2 ml-1">Nama Item</label>
                        <input type="text" name="nama_item" x-model="editItem.nama_item" required class="w-full border-gray-200 rounded-2xl shadow-sm focus:ring-emerald-500/20 focus:border-emerald-500 px-5 py-4 border bg-white/50 text-sm font-bold">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-emerald-700 mb-2 ml-1">Peruntukan Gender</label>
                        <div class="relative group">
                            <input type="hidden" name="jk" :value="editItem.jk" required>
                            <button type="button" @click="editOpen = !editOpen" @click.away="editOpen = false"
                                    class="w-full flex items-center justify-between px-5 py-4 border border-gray-200 rounded-2xl text-sm transition-all focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 bg-white/50 hover:bg-white font-black text-emerald-800">
                                <span x-text="editGenderLabel"></span>
                                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="editOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </button>

                            <div x-show="editOpen" x-transition x-cloak
                                 class="absolute z-[70] left-0 right-0 mt-2 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden p-2">
                                <template x-for="opt in genderOptions" :key="opt.value">
                                    <div @click="editItem.jk = opt.value; editOpen = false" 
                                         class="flex items-center justify-between px-5 py-3 rounded-xl cursor-pointer hover:bg-emerald-50 transition-all font-black"
                                         :class="editItem.jk == opt.value ? 'bg-emerald-50 text-emerald-700' : 'text-gray-600'">
                                        <span x-text="opt.label" class="text-[10px] uppercase tracking-widest whitespace-nowrap"></span>
                                        <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all ml-4"
                                             :class="editItem.jk == opt.value ? 'border-emerald-500 bg-emerald-500' : 'border-gray-200 bg-white'">
                                            <div x-show="editItem.jk == opt.value" x-transition class="w-1.5 h-1.5 bg-white rounded-full"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-emerald-700 mb-2 ml-1">Satuan</label>
                            <input type="text" name="satuan" x-model="editItem.satuan" required class="w-full border-gray-200 rounded-2xl shadow-sm focus:ring-emerald-500/20 focus:border-emerald-500 px-4 py-3 border bg-white/50 text-sm font-bold">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-emerald-700 mb-2 ml-1">Urutan</label>
                            <input type="number" name="urutan" x-model="editItem.urutan" class="w-full border-gray-200 rounded-2xl shadow-sm focus:ring-emerald-500/20 focus:border-emerald-500 px-4 py-3 border bg-white/50 text-sm font-bold">
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50/50 px-8 py-6 flex justify-end space-x-3 border-t border-gray-100 rounded-b-[2.5rem]">
                    <button type="button" @click="showEdit = false" class="px-6 py-2.5 text-xs font-black uppercase tracking-widest text-gray-400 hover:text-gray-600 transition-all">Batal</button>
                    <button type="submit" class="px-8 py-2.5 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 font-black text-xs uppercase tracking-widest shadow-lg shadow-emerald-100 transition-all active:scale-95">Update Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = "Manajemen Item Seragam - SIT-PSB";
require __DIR__ . '/../layouts/admin.php';
