<?php ob_start(); ?>
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Manajemen Panitia</h1>
        <p class="text-gray-600 mt-1">Kelola akun dan role kepanitiaan PSB.</p>
    </div>
    <button onclick="document.getElementById('modalAdd').classList.remove('hidden')" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded shadow flex items-center">
        + Tambah Panitia
    </button>
</div>

<!-- List Users -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Lengkap</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Username / ID</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role / Jabatan</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($users as $row): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($row['name']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono"><?= htmlspecialchars($row['username']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 uppercase">
                            <?= str_replace('_', ' ', $row['role']) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button onclick='openEditModal(<?= json_encode($row) ?>)' class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                        <?php if ($row['id'] != Auth::user()['id']): ?>
                            <a href="<?= url("admin/delete-user/{$row['id']}") ?>" class="text-red-600 hover:text-red-900" onclick="return confirmLink(event, 'Hapus panitia secara permanen?')">Hapus</a>
                        <?php else: ?>
                            <span class="text-gray-400 italic">Anda</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Add User -->
<div id="modalAdd" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/20 backdrop-blur-sm">
    <div class="bg-white/70 backdrop-blur-xl border border-white/40 shadow-2xl rounded-[2.5rem] max-w-md w-full relative">
        <form action="<?= url('admin/store-user') ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
            <div class="px-8 py-5 border-b border-gray-100 rounded-t-[2.5rem]">
                <h3 class="text-xl font-black text-gray-900">Tambah Akun Panitia</h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Username Login</label>
                    <input type="text" name="username" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp (628...)</label>
                    <input type="text" name="no_wa" placeholder="Contoh: 628123456789" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 px-3 py-2 border">
                </div>
                <!-- Role Jabatan (Custom Premium Dropdown) -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wider text-[10px]">Role Jabatan</label>
                    <div class="relative group" x-data="{ 
                        open: false, 
                        value: 'admin',
                        options: [
                            { label: 'Administrator', value: 'admin' },
                            { label: 'Sekretaris Pendaftaran', value: 'sekretaris' },
                            { label: 'Bendahara Pendaftaran & Tes', value: 'bendahara_reg' },
                            { label: 'Bendahara Daftar Ulang', value: 'bendahara_du' },
                            { label: 'Mufatis (Penguji Lisan)', value: 'mufatis' }
                        ],
                        get selectedLabel() {
                            return this.options.find(o => o.value == this.value)?.label || 'Pilih Role'
                        }
                    }">
                        <input type="hidden" name="role" :value="value" required>
                        <button type="button" @click="open = !open" @click.away="open = false"
                                class="w-full flex items-center justify-between px-5 py-4 border border-gray-200 rounded-2xl text-sm transition-all focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 bg-gray-50/50 hover:bg-white font-bold text-gray-700">
                            <span x-text="selectedLabel"></span>
                            <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </button>

                        <div x-show="open" x-transition x-cloak
                             class="absolute z-[70] left-0 right-0 mt-2 bg-white rounded-[1.5rem] shadow-2xl border border-gray-100 overflow-hidden p-2">
                            <template x-for="item in options" :key="item.value">
                                <div @click="value = item.value; open = false" 
                                     class="flex items-center justify-between px-5 py-3 rounded-xl cursor-pointer hover:bg-emerald-50 transition-all"
                                     :class="value == item.value ? 'bg-emerald-50' : ''">
                                    <span x-text="item.label" class="text-xs font-bold whitespace-nowrap" :class="value == item.value ? 'text-emerald-700' : 'text-gray-600'"></span>
                                    <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all ml-4"
                                         :class="value == item.value ? 'border-emerald-500 bg-emerald-500' : 'border-gray-200 bg-white'">
                                        <div x-show="value == item.value" x-transition class="w-1.5 h-1.5 bg-white rounded-full"></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50/50 px-8 py-5 flex justify-end space-x-3 border-t border-gray-100 rounded-b-[2.5rem]">
                <button type="button" onclick="document.getElementById('modalAdd').classList.add('hidden')" class="px-6 py-2.5 text-sm font-bold text-gray-400 hover:text-gray-600 transition-colors uppercase tracking-widest">Batal</button>
                <button type="submit" class="px-8 py-2.5 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 font-bold shadow-lg shadow-emerald-100 transition-all active:scale-95">Simpan Panitia</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit User -->
<div id="modalEdit" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/20 backdrop-blur-sm">
    <div class="bg-white/70 backdrop-blur-xl border border-white/40 shadow-2xl rounded-[2.5rem] max-w-md w-full relative">
        <form id="formEdit" action="" method="POST">
            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
            <div class="px-8 py-5 border-b border-gray-100 rounded-t-[2.5rem]">
                <h3 class="text-xl font-black text-gray-900">Edit Akun Panitia</h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" id="edit_name" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Username Login</label>
                    <input type="text" name="username" id="edit_username" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru (Kosongkan jika tidak diubah)</label>
                    <input type="password" name="password" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp (628...)</label>
                    <input type="text" name="no_wa" id="edit_no_wa" placeholder="Contoh: 628123456789" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 px-3 py-2 border">
                </div>
                <!-- Role Jabatan (Custom Premium Dropdown) -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wider text-[10px]">Role Jabatan</label>
                    <div class="relative group" x-data="{ 
                        open: false, 
                        value: '',
                        options: [
                            { label: 'Administrator', value: 'admin' },
                            { label: 'Sekretaris Pendaftaran', value: 'sekretaris' },
                            { label: 'Bendahara Pendaftaran & Tes', value: 'bendahara_reg' },
                            { label: 'Bendahara Daftar Ulang', value: 'bendahara_du' },
                            { label: 'Mufatis (Penguji Lisan)', value: 'mufatis' }
                        ],
                        get selectedLabel() {
                            return this.options.find(o => o.value == this.value)?.label || 'Pilih Role'
                        }
                    }" @set-role.window="value = $event.detail">
                        <input type="hidden" name="role" :value="value" required>
                        <button type="button" @click="open = !open" @click.away="open = false"
                                class="w-full flex items-center justify-between px-5 py-4 border border-gray-200 rounded-2xl text-sm transition-all focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 bg-gray-50/50 hover:bg-white font-bold text-gray-700">
                            <span x-text="selectedLabel"></span>
                            <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </button>

                        <div x-show="open" x-transition x-cloak
                             class="absolute z-[70] left-0 right-0 mt-2 bg-white rounded-[1.5rem] shadow-2xl border border-gray-100 overflow-hidden p-2">
                            <template x-for="item in options" :key="item.value">
                                <div @click="value = item.value; open = false" 
                                     class="flex items-center justify-between px-5 py-3 rounded-xl cursor-pointer hover:bg-emerald-50 transition-all"
                                     :class="value == item.value ? 'bg-emerald-50' : ''">
                                    <span x-text="item.label" class="text-xs font-bold whitespace-nowrap" :class="value == item.value ? 'text-emerald-700' : 'text-gray-600'"></span>
                                    <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all ml-4"
                                         :class="value == item.value ? 'border-emerald-500 bg-emerald-500' : 'border-gray-200 bg-white'">
                                        <div x-show="value == item.value" x-transition class="w-1.5 h-1.5 bg-white rounded-full"></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50/50 px-8 py-5 flex justify-end space-x-3 border-t border-gray-100 rounded-b-[2.5rem]">
                <button type="button" onclick="document.getElementById('modalEdit').classList.add('hidden')" class="px-6 py-2.5 text-sm font-bold text-gray-400 hover:text-gray-600 transition-colors uppercase tracking-widest">Batal</button>
                <button type="submit" class="px-8 py-2.5 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 font-bold shadow-lg shadow-emerald-100 transition-all active:scale-95">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(user) {
    document.getElementById('edit_name').value = user.name;
    document.getElementById('edit_username').value = user.username;
    document.getElementById('edit_no_wa').value = user.no_wa || '';
    // Dispatch event to update Alpine custom dropdown
    window.dispatchEvent(new CustomEvent('set-role', { detail: user.role }));
    document.getElementById('formEdit').action = '<?= url("admin/update-user/") ?>' + user.id;
    document.getElementById('modalEdit').classList.remove('hidden');
}
</script>

<?php
$content = ob_get_clean();
$title = "Manajemen Panitia - SIT-PSB";
require __DIR__ . '/../layouts/admin.php';
