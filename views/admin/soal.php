<?php ob_start(); ?>
<div x-data="{ 
    showAdd: false, 
    showEdit: false, 
    addGelOpen: false,
    addKunciOpen: false,
    editGelOpen: false,
    editKunciOpen: false,
    
    // Quran Dropdown States
    addSurahOpen: false,
    addAyatStartOpen: false,
    addAyatEndOpen: false,
    editSurahOpen: false,
    editAyatStartOpen: false,
    editAyatEndOpen: false,

    addGelId: '<?= $gelombang[0]['id'] ?? '' ?>',
    addKunci: 'A',
    addTipe: 'pg',
    
    // Quran Integration State
    surahs: [],
    addIsQuran: false,
    addSurahNo: '',
    addSurahName: '',
    addAyatStart: 1,
    addAyatEnd: 1,
    addMaxAyat: 1,
    
    editSoal: { id: '', gelombang_id: '', tipe: 'pg', pertanyaan: '', urutan: 0, pilihan_A: '', pilihan_B: '', pilihan_C: '', pilihan_D: '', jawaban_benar: 'A', is_quran: false, surah_no: '', surah_name: '', ayat_start: 1, ayat_end: 1, maxAyat: 1 },
    
    async init() {
        try {
            const res = await fetch('https://equran.id/api/v2/surat');
            const data = await res.json();
            this.surahs = data.data;
        } catch (e) { console.error('Gagal memuat daftar surah', e); }
    },
    
    get addGelLabel() {
        let gels = <?= htmlspecialchars(json_encode($gelombang)) ?>;
        return gels.find(g => g.id == this.addGelId)?.nama || 'Pilih Gelombang';
    },
    get addKunciLabel() {
        return 'Opsi ' + this.addKunci;
    },
    get editGelLabel() {
        let gels = <?= htmlspecialchars(json_encode($gelombang)) ?>;
        return gels.find(g => g.id == this.editSoal.gelombang_id)?.nama || 'Pilih Gelombang';
    },
    get editKunciLabel() {
        return 'Opsi ' + this.editSoal.jawaban_benar;
    },
    
    get addSurahLabel() {
        return this.surahs.find(s => s.nomor == this.addSurahNo)?.namaLatin || 'Pilih Surah';
    },
    
    get editSurahLabel() {
        return this.surahs.find(s => s.nomor == this.editSoal.surah_no)?.namaLatin || 'Pilih Surah';
    },
    
    async updateMaxAyat(surahNo, mode) {
        if (!surahNo) return;
        try {
            const res = await fetch(`https://equran.id/api/v2/surat/${surahNo}`);
            const data = await res.json();
            if (mode === 'add') {
                this.addMaxAyat = data.data.jumlahAyat;
                this.addSurahName = data.data.namaLatin;
                if (this.addAyatStart > this.addMaxAyat) this.addAyatStart = 1;
                if (this.addAyatEnd > this.addMaxAyat) this.addAyatEnd = this.addMaxAyat;
            } else {
                this.editSoal.maxAyat = data.data.jumlahAyat;
                this.editSoal.surah_name = data.data.namaLatin;
                if (this.editSoal.ayat_start > this.editSoal.maxAyat) this.editSoal.ayat_start = 1;
                if (this.editSoal.ayat_end > this.editSoal.maxAyat) this.editSoal.ayat_end = this.editSoal.maxAyat;
            }
        } catch (e) { console.error('Gagal update info surah', e); }
    },
    
    openEdit(item) {
        let pilihan = JSON.parse(item.pilihan_json || '{}');
        this.editSoal = { 
            id: item.id,
            gelombang_id: item.gelombang_id,
            tipe: item.tipe,
            pertanyaan: item.pertanyaan,
            urutan: item.urutan,
            pilihan_A: pilihan.A || '',
            pilihan_B: pilihan.B || '',
            pilihan_C: pilihan.C || '',
            pilihan_D: pilihan.D || '',
            jawaban_benar: item.jawaban_benar || 'A',
            is_quran: pilihan.is_quran || false,
            surah_no: pilihan.surah_no || '',
            surah_name: pilihan.surah_name || '',
            ayat_start: pilihan.ayat_start || 1,
            ayat_end: pilihan.ayat_end || 1,
            maxAyat: 1
        };
        if (this.editSoal.surah_no) this.updateMaxAyat(this.editSoal.surah_no, 'edit');
        this.showEdit = true;
        this.editGelOpen = false;
        this.editKunciOpen = false;
        this.editSurahOpen = false;
        this.editAyatStartOpen = false;
        this.editAyatEndOpen = false;
    }
}">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Bank Soal CBT</h1>
            <p class="text-gray-600 mt-1">Kelola pertanyaan ujian untuk tiap gelombang.</p>
        </div>
        <button @click="showAdd = true; addGelOpen = false; addKunciOpen = false; addSurahOpen = false" class="bg-emerald-600 hover:bg-emerald-700 text-white font-black py-2.5 px-6 rounded-2xl shadow-lg shadow-emerald-100 flex items-center transition-all active:scale-95 uppercase text-xs tracking-widest">
            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            Tambah Soal
        </button>
    </div>

    <!-- Filter Gelombang -->
    <div class="mb-8 p-6 bg-white/50 backdrop-blur-sm rounded-3xl border border-white/60 shadow-sm flex flex-wrap gap-4 items-center">
        <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Filter Gelombang:</span>
        <div class="flex gap-2">
            <a href="<?= url('admin/soal') ?>" class="px-4 py-2 rounded-xl text-xs font-black transition-all <?= !isset($_GET['gelombang_id']) ? 'bg-emerald-600 text-white shadow-md' : 'bg-white text-gray-500 hover:bg-emerald-50' ?>">SEMUA</a>
            <?php foreach($gelombang as $g): ?>
                <a href="<?= url('admin/soal?gelombang_id='.$g['id']) ?>" class="px-4 py-2 rounded-xl text-xs font-black transition-all <?= (isset($_GET['gelombang_id']) && $_GET['gelombang_id'] == $g['id']) ? 'bg-emerald-600 text-white shadow-md' : 'bg-white text-gray-500 hover:bg-emerald-50' ?>"><?= htmlspecialchars($g['nama']) ?></a>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="space-y-6">
        <?php 
        $current_gel = null;
        foreach ($soal as $row): 
            $pilihan = json_decode($row['pilihan_json'], true);
            if ($current_gel !== $row['gelombang_id']):
                $current_gel = $row['gelombang_id'];
        ?>
            <div class="bg-gray-100 px-6 py-3 border-l-4 border-emerald-500 font-black text-xs uppercase tracking-widest text-emerald-800">
                <?= htmlspecialchars($row['nama_gel'] ?? 'Gelombang Tidak Diketahui') ?>
            </div>
        <?php endif; ?>
        
        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-6 sm:p-8 relative hover:shadow-md transition-shadow">
            <!-- Header: Nomor, Badge, dan Aksi -->
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6 pb-6 border-b border-gray-50">
                <div class="flex items-center gap-4">
                    <span class="text-3xl font-black text-gray-200">#<?= str_pad($row['urutan'], 2, '0', STR_PAD_LEFT) ?></span>
                    <div class="flex flex-wrap gap-2">
                        <span class="px-3 py-1 text-[10px] font-black rounded-full bg-gray-100 text-gray-500 uppercase tracking-widest">Urutan: <?= $row['urutan'] ?></span>
                        <span class="px-3 py-1 text-[10px] font-black rounded-full <?= $row['tipe'] == 'pg' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' ?> uppercase tracking-widest">
                            <?= strtoupper($row['tipe'] == 'pg' ? 'Pilihan Ganda' : 'Oral/Rekam') ?>
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-4 pt-4 lg:pt-0 border-t lg:border-t-0 lg:border-l lg:pl-6 border-gray-100">
                    <button @click="openEdit(<?= htmlspecialchars(json_encode($row)) ?>)" class="text-emerald-600 hover:text-emerald-800 font-black uppercase text-[10px] tracking-widest flex items-center bg-emerald-50 hover:bg-emerald-100 px-4 py-2 rounded-xl transition-all">
                        <svg class="w-3.5 h-3.5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        Edit
                    </button>
                    <a href="<?= url("admin/delete-soal/{$row['id']}") ?>" class="text-red-600 hover:text-red-800 font-black uppercase text-[10px] tracking-widest flex items-center bg-red-50 hover:bg-red-100 px-4 py-2 rounded-xl transition-all" onclick="return confirmLink(event, 'Hapus soal secara permanen?')">
                        <svg class="w-3.5 h-3.5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        Hapus
                    </a>
                </div>
            </div>
            
            <div class="flex-1">
                <div class="prose max-w-none mb-8 text-gray-800 font-medium leading-relaxed whitespace-normal break-words">
                    <?= safe_html($row['pertanyaan']) ?>
                </div>
                
                <?php if($row['tipe'] == 'pg'): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                        <?php foreach(['A', 'B', 'C', 'D'] as $opsi): ?>
                        <?php if(isset($pilihan[$opsi])): ?>
                            <div class="px-6 py-4 rounded-2xl border transition-all <?= $row['jawaban_benar'] == $opsi ? 'bg-emerald-50 border-emerald-200 shadow-sm' : 'bg-gray-50 border-gray-100' ?> flex items-center">
                                <span class="w-8 h-8 rounded-xl <?= $row['jawaban_benar'] == $opsi ? 'bg-emerald-500 text-white' : 'bg-gray-200 text-gray-500' ?> flex items-center justify-center text-xs font-black mr-4"><?= $opsi ?></span>
                                <span class="text-sm <?= $row['jawaban_benar'] == $opsi ? 'font-black text-emerald-800' : 'text-gray-600 font-medium' ?>"><?= htmlspecialchars($pilihan[$opsi]) ?></span>
                                <?php if($row['jawaban_benar'] == $opsi): ?>
                                    <svg class="w-5 h-5 text-emerald-500 ml-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php elseif($row['tipe'] == 'rekam_suara'): ?>
                    <?php if(isset($pilihan['is_quran']) && $pilihan['is_quran']): ?>
                        <div class="mt-6 p-6 sm:p-8 bg-emerald-50/30 border border-emerald-100 rounded-[2.5rem]" x-data="{ 
                            ayatText: '', 
                            loading: true,
                            async fetchText() {
                                try {
                                    const res = await fetch('https://equran.id/api/v2/surat/<?= $pilihan['surah_no'] ?>');
                                    const data = await res.json();
                                    const verses = data.data.ayat.filter(a => a.nomorAyat >= <?= $pilihan['ayat_start'] ?> && a.nomorAyat <= <?= $pilihan['ayat_end'] ?>);
                                    this.ayatText = verses.map(v => v.teksArab + ' (' + v.nomorAyat + ')').join(' ');
                                    this.loading = false;
                                } catch(e) { this.ayatText = 'Gagal memuat teks Arabic'; this.loading = false; }
                            }
                        }" x-init="fetchText()">
                            <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-6">
                                <span class="text-[10px] font-black uppercase tracking-widest text-emerald-600 bg-emerald-100/50 px-3 py-1 rounded-lg w-fit">Tes Hafalan: Surah <?= $pilihan['surah_name'] ?> (Ayat <?= $pilihan['ayat_start'] ?>-<?= $pilihan['ayat_end'] ?>)</span>
                                <span class="text-[10px] font-black uppercase tracking-widest text-emerald-400">Teks Arabic</span>
                            </div>
                            <div x-show="loading" class="animate-pulse space-y-3">
                                <div class="h-4 bg-emerald-100/50 rounded w-full"></div>
                                <div class="h-4 bg-emerald-100/50 rounded w-3/4 ml-auto"></div>
                            </div>
                            <div x-show="!loading" class="text-right text-2xl sm:text-3xl font-arabic leading-[3.5rem] sm:leading-[4.5rem] text-emerald-900 dir-rtl">
                                <span x-text="ayatText"></span>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="bg-purple-50/50 border border-purple-100/50 p-6 rounded-2xl text-[11px] font-black uppercase tracking-widest text-purple-700 flex items-center mt-4 shadow-sm">
                            <div class="p-3 bg-purple-100 rounded-xl mr-4 text-purple-600">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" /></svg>
                            </div>
                            Tipe: Ujian Lisan / Setoran Hafalan Tanpa Teks Khusus
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
        
        <?php if(empty($soal)): ?>
            <div class="text-center py-20 bg-white border border-dashed rounded-[2.5rem] text-gray-400 font-black uppercase tracking-widest text-[10px]">Belum ada soal ditambahkan.</div>
        <?php endif; ?>
    </div>

    <!-- Modal Add Soal -->
    <div x-show="showAdd" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/20 backdrop-blur-sm overflow-y-auto" x-cloak>
        <div class="bg-white/70 backdrop-blur-xl border border-white/40 shadow-2xl rounded-[2.5rem] max-w-2xl w-full my-8 relative flex flex-col whitespace-normal" @click.away="showAdd = false">
            <form action="<?= url('admin/store-soal') ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                <div class="px-8 py-6 border-b border-gray-100 flex-shrink-0 rounded-t-[2.5rem]">
                    <h3 class="text-xl font-black text-gray-900">Setup Item Soal Baru</h3>
                </div>
                <div class="p-8 space-y-6 overflow-y-auto max-h-[60vh] custom-scrollbar">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-emerald-700 mb-2 ml-1">Target Gelombang</label>
                            <div class="relative">
                                <input type="hidden" name="gelombang_id" :value="addGelId" required>
                                <button type="button" @click="addGelOpen = !addGelOpen" @click.away="addGelOpen = false"
                                        class="w-full flex items-center justify-between px-5 py-4 border border-gray-200 rounded-2xl text-sm transition-all focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 bg-white/50 hover:bg-white font-black text-emerald-800">
                                    <span x-text="addGelLabel"></span>
                                    <svg class="w-4 h-4 text-gray-400 transition-transform" :class="addGelOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </button>
                                <div x-show="addGelOpen" x-transition x-cloak class="absolute z-[70] left-0 right-0 mt-2 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden p-2">
                                    <?php foreach($gelombang as $g): ?>
                                        <div @click="addGelId = '<?= $g['id'] ?>'; addGelOpen = false" 
                                             class="flex items-center justify-between px-5 py-3 rounded-xl cursor-pointer hover:bg-emerald-50 transition-all font-black"
                                             :class="addGelId == '<?= $g['id'] ?>' ? 'bg-emerald-50 text-emerald-700' : 'text-gray-600'">
                                            <span class="text-[10px] uppercase tracking-widest whitespace-nowrap"><?= htmlspecialchars($g['nama']) ?></span>
                                            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all ml-4"
                                                 :class="addGelId == '<?= $g['id'] ?>' ? 'border-emerald-500 bg-emerald-500' : 'border-gray-200 bg-white'">
                                                <div x-show="addGelId == '<?= $g['id'] ?>'" x-transition class="w-1.5 h-1.5 bg-white rounded-full"></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-emerald-700 mb-2 ml-1">Urutan Tampil</label>
                            <input type="number" name="urutan" value="0" class="w-full border-gray-200 rounded-2xl focus:ring-emerald-500/20 focus:border-emerald-500 px-5 py-4 border font-bold text-gray-700 bg-white/50 hover:bg-white transition-all text-sm">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-emerald-700 mb-3 ml-1">Jenis Pertanyaan</label>
                        <div class="flex p-1.5 bg-gray-100/50 backdrop-blur-sm rounded-2xl border border-gray-200/50">
                            <input type="hidden" name="tipe" :value="addTipe">
                            <button type="button" @click="addTipe = 'pg'" :class="addTipe == 'pg' ? 'bg-white shadow-md text-emerald-600 scale-[1.02]' : 'text-gray-400 hover:text-gray-600'" class="flex-1 py-3 px-4 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all duration-300 flex items-center justify-center">
                                Pilihan Ganda
                            </button>
                            <button type="button" @click="addTipe = 'rekam_suara'" :class="addTipe == 'rekam_suara' ? 'bg-white shadow-md text-purple-600 scale-[1.02]' : 'text-gray-400 hover:text-gray-600'" class="flex-1 py-3 px-4 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all duration-300 flex items-center justify-center">
                                Rekaman Suara
                            </button>
                        </div>
                    </div>

                    <div x-show="addTipe == 'rekam_suara'" x-transition class="p-6 bg-purple-50/30 border border-purple-100 rounded-3xl space-y-4">
                        <label class="flex items-center space-x-3 cursor-pointer group">
                            <input type="checkbox" name="is_quran" value="1" x-model="addIsQuran" class="w-5 h-5 rounded-lg border-purple-300 text-purple-600 focus:ring-purple-500/20 transition-all">
                            <span class="text-xs font-black uppercase tracking-widest text-purple-700 group-hover:text-purple-900 transition-colors">Gunakan Materi Qur'an (Hafalan)</span>
                        </label>
                        
                        <div x-show="addIsQuran" x-transition class="grid grid-cols-1 gap-4 mt-4 pt-4 border-t border-purple-100">
                            <!-- Surah Dropdown -->
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-widest text-purple-500 mb-2 ml-1">Pilih Surah</label>
                                <div class="relative">
                                    <input type="hidden" name="surah_no" :value="addSurahNo">
                                    <input type="hidden" name="surah_name" :value="addSurahName">
                                    <button type="button" @click="addSurahOpen = !addSurahOpen" @click.away="addSurahOpen = false"
                                            class="w-full flex items-center justify-between px-5 py-4 border border-purple-100 rounded-2xl text-sm transition-all focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 bg-white/50 hover:bg-white font-black text-purple-800">
                                        <span x-text="addSurahLabel"></span>
                                        <svg class="w-4 h-4 text-purple-400 transition-transform" :class="addSurahOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                    </button>
                                    <div x-show="addSurahOpen" x-transition x-cloak class="absolute z-[80] left-0 right-0 mt-2 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden p-2 max-h-60 overflow-y-auto custom-scrollbar">
                                        <template x-for="s in surahs" :key="s.nomor">
                                            <div @click="addSurahNo = s.nomor; addSurahName = s.namaLatin; addSurahOpen = false; updateMaxAyat(s.nomor, 'add')" 
                                                 class="flex items-center justify-between px-5 py-3 rounded-xl cursor-pointer hover:bg-purple-50 transition-all font-black"
                                                 :class="addSurahNo == s.nomor ? 'bg-purple-50 text-purple-700' : 'text-gray-600'">
                                                <span class="text-[10px] uppercase tracking-widest" x-text="s.nomor + '. ' + s.namaLatin"></span>
                                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all ml-4"
                                                     :class="addSurahNo == s.nomor ? 'border-purple-500 bg-purple-500' : 'border-gray-200 bg-white'">
                                                    <div x-show="addSurahNo == s.nomor" x-transition class="w-1.5 h-1.5 bg-white rounded-full"></div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <!-- Ayat Mulai -->
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-purple-500 mb-2 ml-1">Ayat Mulai</label>
                                    <div class="relative">
                                        <input type="hidden" name="ayat_start" :value="addAyatStart">
                                        <button type="button" @click="addAyatStartOpen = !addAyatStartOpen" @click.away="addAyatStartOpen = false"
                                                class="w-full flex items-center justify-between px-5 py-3.5 border border-purple-100 rounded-2xl text-xs transition-all focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 bg-white/50 hover:bg-white font-black text-purple-800">
                                            <span x-text="'Ayat ' + addAyatStart"></span>
                                            <svg class="w-4 h-4 text-purple-400 transition-transform" :class="addAyatStartOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                        </button>
                                        <div x-show="addAyatStartOpen" x-transition x-cloak class="absolute z-[75] left-0 right-0 mt-2 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden p-2 max-h-48 overflow-y-auto custom-scrollbar">
                                            <template x-for="n in addMaxAyat" :key="n">
                                                <div @click="addAyatStart = n; addAyatStartOpen = false" 
                                                     class="flex items-center justify-between px-5 py-2 rounded-lg cursor-pointer hover:bg-purple-50 transition-all font-black"
                                                     :class="addAyatStart == n ? 'bg-purple-50 text-purple-700' : 'text-gray-600'">
                                                    <span class="text-[10px] uppercase tracking-widest" x-text="'Ayat ' + n"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                <!-- Ayat Selesai -->
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-purple-500 mb-2 ml-1">Ayat Selesai</label>
                                    <div class="relative">
                                        <input type="hidden" name="ayat_end" :value="addAyatEnd">
                                        <button type="button" @click="addAyatEndOpen = !addAyatEndOpen" @click.away="addAyatEndOpen = false"
                                                class="w-full flex items-center justify-between px-5 py-3.5 border border-purple-100 rounded-2xl text-xs transition-all focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 bg-white/50 hover:bg-white font-black text-purple-800">
                                            <span x-text="'Ayat ' + addAyatEnd"></span>
                                            <svg class="w-4 h-4 text-purple-400 transition-transform" :class="addAyatEndOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                        </button>
                                        <div x-show="addAyatEndOpen" x-transition x-cloak class="absolute z-[75] left-0 right-0 mt-2 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden p-2 max-h-48 overflow-y-auto custom-scrollbar">
                                            <template x-for="n in addMaxAyat" :key="n">
                                                <div @click="addAyatEnd = n; addAyatEndOpen = false" 
                                                     class="flex items-center justify-between px-5 py-2 rounded-lg cursor-pointer hover:bg-purple-50 transition-all font-black"
                                                     :class="addAyatEnd == n ? 'bg-purple-50 text-purple-700' : 'text-gray-600'">
                                                    <span class="text-[10px] uppercase tracking-widest" x-text="'Ayat ' + n"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-emerald-700 mb-2 ml-1">Pertanyaan (Judul/Instruksi)</label>
                        <textarea name="pertanyaan" rows="2" required class="w-full border-gray-200 rounded-2xl focus:ring-emerald-500/20 focus:border-emerald-500 px-5 py-4 border font-mono text-sm bg-white/50 hover:bg-white transition-all text-gray-800" placeholder="Contoh: Hafalkan ayat berikut dengan tartil..."></textarea>
                    </div>

                    <div x-show="addTipe == 'pg'" x-transition class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div><label class="block text-[10px] font-black text-gray-400 mb-1 ml-1 uppercase">Opsi A</label><input type="text" name="pilihan_A" :required="addTipe == 'pg'" class="w-full border-gray-200 rounded-2xl px-4 py-3 border bg-white/50 text-sm font-bold"></div>
                            <div><label class="block text-[10px] font-black text-gray-400 mb-1 ml-1 uppercase">Opsi B</label><input type="text" name="pilihan_B" :required="addTipe == 'pg'" class="w-full border-gray-200 rounded-2xl px-4 py-3 border bg-white/50 text-sm font-bold"></div>
                            <div><label class="block text-[10px] font-black text-gray-400 mb-1 ml-1 uppercase">Opsi C</label><input type="text" name="pilihan_C" :required="addTipe == 'pg'" class="w-full border-gray-200 rounded-2xl px-4 py-3 border bg-white/50 text-sm font-bold"></div>
                            <div><label class="block text-[10px] font-black text-gray-400 mb-1 ml-1 uppercase">Opsi D</label><input type="text" name="pilihan_D" :required="addTipe == 'pg'" class="w-full border-gray-200 rounded-2xl px-4 py-3 border bg-white/50 text-sm font-bold"></div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-blue-700 mb-2 ml-1">Kunci Jawaban</label>
                            <div class="relative">
                                <input type="hidden" name="jawaban_benar" :value="addKunci" :required="addTipe == 'pg'">
                                <button type="button" @click="addKunciOpen = !addKunciOpen" @click.away="addKunciOpen = false"
                                        class="w-full flex items-center justify-between px-5 py-4 border border-gray-200 rounded-2xl text-sm transition-all focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-blue-50/30 hover:bg-white font-black text-blue-700">
                                    <span x-text="addKunciLabel"></span>
                                    <svg class="w-4 h-4 text-blue-400 transition-transform" :class="addKunciOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </button>
                                <div x-show="addKunciOpen" x-transition x-cloak class="absolute z-[70] left-0 right-0 mt-2 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden p-2">
                                    <?php foreach(['A', 'B', 'C', 'D'] as $o): ?>
                                        <div @click="addKunci = '<?= $o ?>'; addKunciOpen = false" 
                                             class="flex items-center justify-between px-5 py-3 rounded-xl cursor-pointer hover:bg-blue-50 transition-all font-black"
                                             :class="addKunci == '<?= $o ?>' ? 'bg-blue-50 text-blue-700' : 'text-gray-600'">
                                            <span class="text-[10px] uppercase tracking-widest">Opsi <?= $o ?></span>
                                            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all ml-4"
                                                 :class="addKunci == '<?= $o ?>' ? 'border-blue-500 bg-blue-500' : 'border-gray-200 bg-white'">
                                                <div x-show="addKunci == '<?= $o ?>'" x-transition class="w-1.5 h-1.5 bg-white rounded-full"></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50/50 px-8 py-6 flex justify-end space-x-3 flex-shrink-0 border-t border-gray-100 rounded-b-[2.5rem]">
                    <button type="button" @click="showAdd = false" class="px-6 py-2.5 text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-gray-600 transition-colors">Batal</button>
                    <button type="submit" class="px-10 py-2.5 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 font-black text-[11px] uppercase tracking-widest shadow-lg shadow-emerald-100 transition-all active:scale-95">Simpan Soal</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Soal -->
    <div x-show="showEdit" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/20 backdrop-blur-sm overflow-y-auto" x-cloak>
        <div class="bg-white/70 backdrop-blur-xl border border-white/40 shadow-2xl rounded-[2.5rem] max-w-2xl w-full my-8 relative flex flex-col whitespace-normal" @click.away="showEdit = false">
            <form :action="'<?= url('admin/update-soal/') ?>' + editSoal.id" method="POST">
                <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                <div class="px-8 py-6 border-b border-gray-100 flex-shrink-0 rounded-t-[2.5rem]">
                    <h3 class="text-xl font-black text-gray-900">Edit Item Soal</h3>
                </div>
                <div class="p-8 space-y-6 overflow-y-auto max-h-[60vh] custom-scrollbar">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-emerald-700 mb-2 ml-1">Target Gelombang</label>
                            <div class="relative">
                                <input type="hidden" name="gelombang_id" :value="editSoal.gelombang_id" required>
                                <button type="button" @click="editGelOpen = !editGelOpen" @click.away="editGelOpen = false"
                                        class="w-full flex items-center justify-between px-5 py-4 border border-gray-200 rounded-2xl text-sm transition-all focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 bg-white/50 hover:bg-white font-black text-emerald-800">
                                    <span x-text="editGelLabel"></span>
                                    <svg class="w-4 h-4 text-gray-400 transition-transform" :class="editGelOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </button>
                                <div x-show="editGelOpen" x-transition x-cloak class="absolute z-[70] left-0 right-0 mt-2 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden p-2">
                                    <?php foreach($gelombang as $g): ?>
                                        <div @click="editSoal.gelombang_id = '<?= $g['id'] ?>'; editGelOpen = false" 
                                             class="flex items-center justify-between px-5 py-3 rounded-xl cursor-pointer hover:bg-emerald-50 transition-all font-black"
                                             :class="editSoal.gelombang_id == '<?= $g['id'] ?>' ? 'bg-emerald-50 text-emerald-700' : 'text-gray-600'">
                                            <span class="text-[10px] uppercase tracking-widest whitespace-nowrap"><?= htmlspecialchars($g['nama']) ?></span>
                                            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all ml-4"
                                                 :class="editSoal.gelombang_id == '<?= $g['id'] ?>' ? 'border-emerald-500 bg-emerald-500' : 'border-gray-200 bg-white'">
                                                <div x-show="editSoal.gelombang_id == '<?= $g['id'] ?>'" x-transition class="w-1.5 h-1.5 bg-white rounded-full"></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-emerald-700 mb-2 ml-1">Urutan Tampil</label>
                            <input type="number" name="urutan" x-model="editSoal.urutan" class="w-full border-gray-200 rounded-2xl focus:ring-emerald-500/20 focus:border-emerald-500 px-5 py-4 border font-bold text-gray-700 bg-white/50 hover:bg-white transition-all text-sm">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-emerald-700 mb-3 ml-1">Jenis Pertanyaan</label>
                        <div class="flex p-1.5 bg-gray-100/50 backdrop-blur-sm rounded-2xl border border-gray-200/50">
                            <input type="hidden" name="tipe" :value="editSoal.tipe">
                            <button type="button" @click="editSoal.tipe = 'pg'" :class="editSoal.tipe == 'pg' ? 'bg-white shadow-md text-emerald-600 scale-[1.02]' : 'text-gray-400 hover:text-gray-600'" class="flex-1 py-3 px-4 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all duration-300 flex items-center justify-center">
                                Pilihan Ganda
                            </button>
                            <button type="button" @click="editSoal.tipe = 'rekam_suara'" :class="editSoal.tipe == 'rekam_suara' ? 'bg-white shadow-md text-purple-600 scale-[1.02]' : 'text-gray-400 hover:text-gray-600'" class="flex-1 py-3 px-4 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all duration-300 flex items-center justify-center">
                                Rekaman Suara
                            </button>
                        </div>
                    </div>

                    <div x-show="editSoal.tipe == 'rekam_suara'" x-transition class="p-6 bg-purple-50/30 border border-purple-100 rounded-3xl space-y-4">
                        <label class="flex items-center space-x-3 cursor-pointer group">
                            <input type="checkbox" name="is_quran" value="1" x-model="editSoal.is_quran" class="w-5 h-5 rounded-lg border-purple-300 text-purple-600 focus:ring-purple-500/20 transition-all">
                            <span class="text-xs font-black uppercase tracking-widest text-purple-700 group-hover:text-purple-900 transition-colors">Gunakan Materi Qur'an (Hafalan)</span>
                        </label>
                        
                        <div x-show="editSoal.is_quran" x-transition class="grid grid-cols-1 gap-4 mt-4 pt-4 border-t border-purple-100">
                            <!-- Edit Surah Dropdown -->
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-widest text-purple-500 mb-2 ml-1">Pilih Surah</label>
                                <div class="relative">
                                    <input type="hidden" name="surah_no" :value="editSoal.surah_no">
                                    <input type="hidden" name="surah_name" :value="editSoal.surah_name">
                                    <button type="button" @click="editSurahOpen = !editSurahOpen" @click.away="editSurahOpen = false"
                                            class="w-full flex items-center justify-between px-5 py-4 border border-purple-100 rounded-2xl text-sm transition-all focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 bg-white/50 hover:bg-white font-black text-purple-800">
                                        <span x-text="editSurahLabel"></span>
                                        <svg class="w-4 h-4 text-purple-400 transition-transform" :class="editSurahOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                    </button>
                                    <div x-show="editSurahOpen" x-transition x-cloak class="absolute z-[80] left-0 right-0 mt-2 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden p-2 max-h-60 overflow-y-auto custom-scrollbar">
                                        <template x-for="s in surahs" :key="s.nomor">
                                            <div @click="editSoal.surah_no = s.nomor; editSoal.surah_name = s.namaLatin; editSurahOpen = false; updateMaxAyat(s.nomor, 'edit')" 
                                                 class="flex items-center justify-between px-5 py-3 rounded-xl cursor-pointer hover:bg-purple-50 transition-all font-black"
                                                 :class="editSoal.surah_no == s.nomor ? 'bg-purple-50 text-purple-700' : 'text-gray-600'">
                                                <span class="text-[10px] uppercase tracking-widest" x-text="s.nomor + '. ' + s.namaLatin"></span>
                                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all ml-4"
                                                     :class="editSoal.surah_no == s.nomor ? 'border-purple-500 bg-purple-500' : 'border-gray-200 bg-white'">
                                                    <div x-show="editSoal.surah_no == s.nomor" x-transition class="w-1.5 h-1.5 bg-white rounded-full"></div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <!-- Edit Ayat Mulai -->
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-purple-500 mb-2 ml-1">Ayat Mulai</label>
                                    <div class="relative">
                                        <input type="hidden" name="ayat_start" :value="editSoal.ayat_start">
                                        <button type="button" @click="editAyatStartOpen = !editAyatStartOpen" @click.away="editAyatStartOpen = false"
                                                class="w-full flex items-center justify-between px-5 py-3.5 border border-purple-100 rounded-2xl text-xs transition-all focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 bg-white/50 hover:bg-white font-black text-purple-800">
                                            <span x-text="'Ayat ' + editSoal.ayat_start"></span>
                                            <svg class="w-4 h-4 text-purple-400 transition-transform" :class="editAyatStartOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                        </button>
                                        <div x-show="editAyatStartOpen" x-transition x-cloak class="absolute z-[75] left-0 right-0 mt-2 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden p-2 max-h-48 overflow-y-auto custom-scrollbar">
                                            <template x-for="n in editSoal.maxAyat" :key="n">
                                                <div @click="editSoal.ayat_start = n; editAyatStartOpen = false" 
                                                     class="flex items-center justify-between px-5 py-2 rounded-lg cursor-pointer hover:bg-purple-50 transition-all font-black"
                                                     :class="editSoal.ayat_start == n ? 'bg-purple-50 text-purple-700' : 'text-gray-600'">
                                                    <span class="text-[10px] uppercase tracking-widest" x-text="'Ayat ' + n"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                <!-- Edit Ayat Selesai -->
                                <div>
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-purple-500 mb-2 ml-1">Ayat Selesai</label>
                                    <div class="relative">
                                        <input type="hidden" name="ayat_end" :value="editSoal.ayat_end">
                                        <button type="button" @click="editAyatEndOpen = !editAyatEndOpen" @click.away="editAyatEndOpen = false"
                                                class="w-full flex items-center justify-between px-5 py-3.5 border border-purple-100 rounded-2xl text-xs transition-all focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 bg-white/50 hover:bg-white font-black text-purple-800">
                                            <span x-text="'Ayat ' + editSoal.ayat_end"></span>
                                            <svg class="w-4 h-4 text-purple-400 transition-transform" :class="editAyatEndOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                        </button>
                                        <div x-show="editAyatEndOpen" x-transition x-cloak class="absolute z-[75] left-0 right-0 mt-2 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden p-2 max-h-48 overflow-y-auto custom-scrollbar">
                                            <template x-for="n in editSoal.maxAyat" :key="n">
                                                <div @click="editSoal.ayat_end = n; editAyatEndOpen = false" 
                                                     class="flex items-center justify-between px-5 py-2 rounded-lg cursor-pointer hover:bg-purple-50 transition-all font-black"
                                                     :class="editSoal.ayat_end == n ? 'bg-purple-50 text-purple-700' : 'text-gray-600'">
                                                    <span class="text-[10px] uppercase tracking-widest" x-text="'Ayat ' + n"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-emerald-700 mb-2 ml-1">Pertanyaan (Judul/Instruksi)</label>
                        <textarea name="pertanyaan" x-model="editSoal.pertanyaan" rows="2" required class="w-full border-gray-200 rounded-2xl focus:ring-emerald-500/20 focus:border-emerald-500 px-5 py-4 border font-mono text-sm bg-white/50 hover:bg-white transition-all text-gray-800"></textarea>
                    </div>

                    <div x-show="editSoal.tipe == 'pg'" x-transition class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div><label class="block text-[10px] font-black text-gray-400 mb-1 ml-1 uppercase">Opsi A</label><input type="text" name="pilihan_A" x-model="editSoal.pilihan_A" :required="editSoal.tipe == 'pg'" class="w-full border-gray-200 rounded-2xl px-4 py-3 border bg-white/50 text-sm font-bold"></div>
                            <div><label class="block text-[10px] font-black text-gray-400 mb-1 ml-1 uppercase">Opsi B</label><input type="text" name="pilihan_B" x-model="editSoal.pilihan_B" :required="editSoal.tipe == 'pg'" class="w-full border-gray-200 rounded-2xl px-4 py-3 border bg-white/50 text-sm font-bold"></div>
                            <div><label class="block text-[10px] font-black text-gray-400 mb-1 ml-1 uppercase">Opsi C</label><input type="text" name="pilihan_C" x-model="editSoal.pilihan_C" :required="editSoal.tipe == 'pg'" class="w-full border-gray-200 rounded-2xl px-4 py-3 border bg-white/50 text-sm font-bold"></div>
                            <div><label class="block text-[10px] font-black text-gray-400 mb-1 ml-1 uppercase">Opsi D</label><input type="text" name="pilihan_D" x-model="editSoal.pilihan_D" :required="editSoal.tipe == 'pg'" class="w-full border-gray-200 rounded-2xl px-4 py-3 border bg-white/50 text-sm font-bold"></div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-blue-700 mb-2 ml-1">Kunci Jawaban</label>
                            <div class="relative">
                                <input type="hidden" name="jawaban_benar" :value="editSoal.jawaban_benar" :required="editSoal.tipe == 'pg'">
                                <button type="button" @click="editKunciOpen = !editKunciOpen" @click.away="editKunciOpen = false"
                                        class="w-full flex items-center justify-between px-5 py-4 border border-gray-200 rounded-2xl text-sm transition-all focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-blue-50/30 hover:bg-white font-black text-blue-700">
                                    <span x-text="editKunciLabel"></span>
                                    <svg class="w-4 h-4 text-blue-400 transition-transform" :class="editKunciOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </button>
                                <div x-show="editKunciOpen" x-transition x-cloak class="absolute z-[70] left-0 right-0 mt-2 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden p-2">
                                    <?php foreach(['A', 'B', 'C', 'D'] as $o): ?>
                                        <div @click="editSoal.jawaban_benar = '<?= $o ?>'; editKunciOpen = false" 
                                             class="flex items-center justify-between px-5 py-3 rounded-xl cursor-pointer hover:bg-blue-50 transition-all font-black"
                                             :class="editSoal.jawaban_benar == '<?= $o ?>' ? 'bg-blue-50 text-blue-700' : 'text-gray-600'">
                                            <span class="text-[10px] uppercase tracking-widest">Opsi <?= $o ?></span>
                                            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all ml-4"
                                                 :class="editSoal.jawaban_benar == '<?= $o ?>' ? 'border-blue-500 bg-blue-500' : 'border-gray-200 bg-white'">
                                                <div x-show="editSoal.jawaban_benar == '<?= $o ?>'" x-transition class="w-1.5 h-1.5 bg-white rounded-full"></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50/50 px-8 py-6 flex justify-end space-x-3 flex-shrink-0 border-t border-gray-100 rounded-b-[2.5rem]">
                    <button type="button" @click="showEdit = false" class="px-6 py-2.5 text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-gray-600 transition-colors">Batal</button>
                    <button type="submit" class="px-10 py-2.5 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 font-black text-[11px] uppercase tracking-widest shadow-lg shadow-emerald-100 transition-all active:scale-95">Update Soal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = "Bank Soal CBT - SIT-PSB";
require __DIR__ . '/../layouts/admin.php';
