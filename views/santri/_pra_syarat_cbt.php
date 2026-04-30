<?php
$template = $settings['template_wa_hafalan'] ?? '';
$template = str_replace('{nama}', $user['name'], $template);
$template = str_replace('{no_tes}', $user['username'], $template);
$template = str_replace('{tahun_ajaran}', $settings['tahun_ajaran'] ?? '', $template);
$encoded_msg = urlencode($template);

$mufatis_name = $mufatis_info['name'] ?? 'Mufatis';
$mufatis_wa = preg_replace('/[^0-9]/', '', $mufatis_info['no_wa'] ?? '');
$wa_url = "https://wa.me/{$mufatis_wa}?text={$encoded_msg}";
?>
<div class="max-w-2xl mx-auto my-8">
    <div class="bg-yellow-50 border border-yellow-300 rounded-lg shadow-sm overflow-hidden">
        <div class="bg-yellow-100 px-6 py-4 border-b border-yellow-200">
            <h3 class="text-xl font-bold text-yellow-800 flex items-center">
                <svg class="h-6 w-6 mr-2 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                Persyaratan Sebelum Ujian
            </h3>
        </div>
        <div class="p-6">
            <p class="text-gray-800 mb-6 font-medium">
                Santri diwajibkan untuk merekam video hafalan Surah Al-Ghasiyah menggunakan kamera HP/Perangkat terlebih dahulu sebelum memulai tes tertulis ini.
            </p>
            
            <p class="text-gray-600 mb-4">
                Kirimkan video tersebut ke Mufatis melalui WhatsApp di bawah ini:
            </p>
            
            <div class="mb-8">
                <p class="font-semibold text-gray-800 text-lg mb-2">📱 <?= htmlspecialchars($mufatis_name) ?></p>
                <a href="<?= $wa_url ?>" target="_blank" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-md shadow-sm transition">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M20.52 3.44A12.028 12.028 0 0012 0C5.38 0 0 5.38 0 12c0 2.12.55 4.16 1.6 5.96L0 24l6.14-1.61A12.067 12.067 0 0012 24c6.62 0 12-5.38 12-12 0-3.21-1.25-6.22-3.48-8.56zM12 21.98c-1.78 0-3.52-.48-5.06-1.38l-.36-.21-3.76.99 1-3.66-.23-.37A9.972 9.972 0 012.02 12c0-5.51 4.49-10 10-10 2.67 0 5.18 1.04 7.07 2.93a9.96 9.96 0 012.91 7.07c0 5.51-4.49 10-9.98 10zm5.49-7.5c-.3-.15-1.78-.88-2.06-.98-.28-.1-.48-.15-.68.15-.2.3-.78.98-.95 1.18-.18.2-.35.23-.65.08-.3-.15-1.27-.47-2.42-1.49-.89-.79-1.49-1.77-1.67-2.07-.18-.3-.02-.46.13-.61.13-.14.3-.35.45-.53.15-.18.2-.3.3-.5.1-.2.05-.38-.02-.53-.08-.15-.68-1.64-.93-2.25-.24-.6-.48-.52-.68-.53h-.58c-.2 0-.53.08-.8.38-.28.3-1.05 1.03-1.05 2.51s1.08 2.91 1.23 3.11c.15.2 2.12 3.24 5.13 4.54 2.16.94 2.87.98 3.96.83 1.21-.17 2.65-1.08 3.03-2.12.38-1.04.38-1.93.26-2.12-.11-.2-.41-.3-.71-.45z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
                    Kirim Video via WhatsApp
                </a>
            </div>

            <hr class="border-yellow-300 mb-6">
            
            <form action="<?= url('santri/kirim-video') ?>" method="POST" x-data="{ checked: false }">
                <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                <label class="flex items-start cursor-pointer mb-6">
                    <div class="flex-shrink-0 mt-1">
                        <input type="checkbox" x-model="checked" required class="h-5 w-5 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded">
                    </div>
                    <div class="ml-3 text-sm text-gray-700">
                        <span class="font-medium">Ya, saya sudah merekam video hafalan Surah Al-Ghasiyah dan video tersebut sudah saya kirimkan kepada Mufatis melalui WhatsApp.</span>
                    </div>
                </label>
                
                <div class="text-center">
                    <button type="submit" :disabled="!checked" :class="checked ? 'bg-blue-600 hover:bg-blue-700 cursor-pointer' : 'bg-gray-400 cursor-not-allowed'" class="text-white font-bold py-3 px-8 rounded-lg shadow transition">
                        ▶️ Simpan & Lanjutkan
                    </button>
                    <p class="text-xs text-gray-500 mt-2 block" x-show="!checked">Silakan centang konfirmasi di atas untuk melanjutkan.</p>
                </div>
            </form>
        </div>
    </div>
</div>
