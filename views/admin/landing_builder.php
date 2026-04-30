<?php ob_start(); ?>
<!-- GrapesJS Core -->
<link href="https://unpkg.com/grapesjs/dist/css/grapes.min.css" rel="stylesheet">
<script src="https://unpkg.com/grapesjs"></script>
<!-- Tailwind Plugin -->
<script src="https://unpkg.com/grapesjs-tailwind"></script>

<style>
    /* Full height editor */
    #gjs {
        height: calc(100vh - 120px);
        margin: 0;
    }
    /* GrapesJS UI Customization to match emerald theme */
    .gjs-one-bg { background-color: #1f2937; }
    .gjs-two-bg { background-color: #111827; }
    .gjs-three-bg { background-color: #374151; }
    .gjs-four-color { color: #10b981; }
    .gjs-four-color-h:hover { color: #34d399; }
</style>

<div class="mb-4 flex justify-between items-center bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Landing Page Visual Builder</h1>
        <p class="text-xs text-gray-500">Gunakan drag-and-drop untuk menyusun tampilan website publik Anda.</p>
    </div>
    <div class="flex items-center gap-3">
        <div class="flex items-center gap-2 mr-4 px-4 py-2 bg-gray-50 rounded-xl border border-gray-100">
            <span class="text-xs font-bold text-gray-500">STATUS LIVE:</span>
            <?php if($config && $config['is_live']): ?>
                <span class="flex h-2 w-2 rounded-full bg-emerald-500"></span>
                <span class="text-xs font-bold text-emerald-600 uppercase">AKTIF</span>
            <?php else: ?>
                <span class="flex h-2 w-2 rounded-full bg-red-500"></span>
                <span class="text-xs font-bold text-red-600 uppercase">DRAFT</span>
            <?php endif; ?>
            <a href="<?= url('admin/toggle-live-builder') ?>" class="ml-2 text-[10px] text-blue-600 hover:underline font-bold">Ubah</a>
        </div>
        <button onclick="saveContent()" id="save-btn" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-6 rounded-xl shadow-lg shadow-emerald-200 transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
            SIMPAN DESAIN
        </button>
    </div>
</div>

<div id="gjs"></div>

<script>
    const editor = grapesjs.init({
        container: '#gjs',
        fromElement: true,
        height: '100%',
        width: 'auto',
        storageManager: false, // We'll handle storage manually
        plugins: ['grapesjs-tailwind'],
        pluginsOpts: {
            'grapesjs-tailwind': {
                tailwindCssUrl: 'https://cdn.tailwindcss.com',
            }
        },
        canvas: {
            styles: [
                'https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700;800&display=swap'
            ]
        }
    });

    // --- CUSTOM BLOCKS ---
    const bm = editor.BlockManager;

    // 1. Header Block
    bm.add('nav-emerald', {
        label: '<b>Emerald Navbar</b>',
        category: 'Sections',
        content: `
            <nav class="w-full bg-white/70 backdrop-blur-xl border-b border-gray-100 py-4 px-6 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="bg-emerald-600 rounded-lg p-2"><svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg></div>
                    <span class="text-xl font-black text-gray-900">SIT-PSB</span>
                </div>
                <div class="hidden md:flex gap-8 text-sm font-bold text-gray-600">
                    <a href="#">BERANDA</a><a href="#">ALUR</a><a href="#">BIAYA</a><a href="#">FAQ</a>
                </div>
                <button class="bg-gray-900 text-white px-6 py-2.5 rounded-xl text-xs font-bold tracking-widest">PORTAL LOGIN</button>
            </nav>
        `
    });

    // 2. Hero Block
    bm.add('hero-emerald', {
        label: '<b>Hero Section</b>',
        category: 'Sections',
        content: `
            <section class="py-20 px-6 bg-emerald-900 text-white relative overflow-hidden">
                <div class="max-w-6xl mx-auto relative z-10">
                    <div class="inline-block bg-emerald-500/20 px-4 py-2 rounded-full text-emerald-400 font-bold text-xs mb-6">PENDAFTARAN TAHUN AJARAN 2024 TELAH DIBUKA</div>
                    <h1 class="text-6xl font-black leading-tight mb-8">Penerimaan Santri Baru <br> <span class="text-emerald-400">Generasi Qur'ani</span></h1>
                    <p class="text-xl text-emerald-100/70 mb-12 max-w-2xl">Membentuk santri yang cerdas, mandiri, dan berakhlakul karimah melalui kurikulum terpadu pesantren modern.</p>
                    <div class="flex gap-4">
                        <button class="bg-emerald-500 text-white px-10 py-4 rounded-2xl font-black text-lg">DAFTAR SEKARANG</button>
                        <button class="bg-white/10 border border-white/20 px-10 py-4 rounded-2xl font-bold text-lg">INFO LENGKAP</button>
                    </div>
                </div>
                <div class="absolute -right-20 -bottom-20 w-96 h-96 bg-emerald-500/20 rounded-full blur-[100px]"></div>
            </section>
        `
    });

    // 3. Feature Card
    bm.add('feature-card', {
        label: '<b>Feature Card</b>',
        category: 'Components',
        content: `
            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm hover:shadow-xl transition-all text-center">
                <div class="w-16 h-16 bg-emerald-50 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                </div>
                <h3 class="text-xl font-black text-gray-900 mb-2">Tahfidz Intensif</h3>
                <p class="text-gray-500 text-sm">Program unggulan menghafal Al-Qur'an dengan metode cepat dan bersanad.</p>
            </div>
        `
    });

    // 4. Divider
    bm.add('divider', {
        label: '<b>Divider Line</b>',
        category: 'Basic',
        content: '<hr class="border-gray-100 my-8">'
    });

    // 5. Popup Section (Modal Trigger)
    bm.add('popup-section', {
        label: '<b>Popup Block</b>',
        category: 'Interactive',
        content: `
            <div class="p-10 bg-blue-600 rounded-[3rem] text-white flex flex-col items-center text-center">
                <h2 class="text-3xl font-black mb-4">Pengumuman Penting!</h2>
                <p class="mb-8 opacity-80">Beasiswa penuh tersedia untuk 10 pendaftar pertama gelombang ini.</p>
                <button class="bg-white text-blue-600 px-8 py-3 rounded-xl font-bold">Cek Selengkapnya</button>
            </div>
        `
    });

    // --- LOAD SAVED CONTENT ---
    <?php if ($config && $config['components_json']): ?>
        editor.setComponents(<?= $config['components_json'] ?>);
        editor.setStyle(<?= json_encode($config['css_content']) ?>);
    <?php endif; ?>

    // --- SAVE LOGIC ---
    async function saveContent() {
        const btn = document.getElementById('save-btn');
        const oldHtml = btn.innerHTML;
        btn.innerHTML = 'MENYIMPAN...';
        btn.disabled = true;

        const html = editor.getHtml();
        const css = editor.getCss();
        const components = JSON.stringify(editor.getComponents());

        try {
            const response = await fetch('<?= url('admin/save-builder') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ html, css, components })
            });
            const result = await response.json();
            
            if (result.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Tersimpan!',
                    text: 'Desain landing page berhasil diperbarui.',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        } catch (e) {
            console.error(e);
            Swal.fire('Error', 'Gagal menyimpan desain.', 'error');
        } finally {
            btn.innerHTML = oldHtml;
            btn.disabled = false;
        }
    }
</script>

<?php
$content = ob_get_clean();
$title = "Landing Builder - Admin SIT-PSB";
require __DIR__ . '/../layouts/admin.php';
?>
