<?php

class LandingController {
    public function index() {
        // Redirect jika sudah login agar langsung ke Dashboard
        if (Auth::check()) {
            $role = Auth::user()['role'];
            $redirectMap = [
                'admin' => 'admin',
                'sekretaris' => 'sekretaris',
                'bendahara_reg' => 'bendahara-reg',
                'bendahara_du' => 'bendahara-du',
                'mufatis' => 'mufatis',
                'santri' => 'santri' // Santri juga diredirect agar praktis
            ];
            
            if (isset($redirectMap[$role])) {
                redirect($redirectMap[$role]);
            }
        }

        $db = Database::getInstance()->getConnection();
        
        // Fetch pengaturan for landing page
        $keys = [
            'hero_gambar', 'hero_judul', 'hero_subjudul', 
            'konten_informasi', 'url_tutorial_video', 
            'konten_syarat', 'konten_biaya', 
            'footer_website', 'footer_email', 
            'footer_instagram', 'footer_facebook', 'wa_narahubung', 
            'nama_pesantren', 'alamat_pesantren', 'list_narahubung',
            'hero_badge_text', 'hero_badge_style', 'hero_badge_animation', 'hero_badge_size',
            'hero_stats_json', 'hero_stats_style', 'hero_images_json'
        ];
        
        $placeholders = str_repeat('?,', count($keys) - 1) . '?';
        $stmt = $db->prepare("SELECT `key`, value FROM pengaturan WHERE `key` IN ($placeholders)");
        $stmt->execute($keys);
        
        $settings = [];
        while ($row = $stmt->fetch()) {
            $settings[$row['key']] = $row['value'];
        }

         // Load all dynamic sections
         $stmtSections = $db->query("SELECT * FROM landing_sections WHERE is_active = 1 ORDER BY order_num ASC");
         $sections = $stmtSections->fetchAll();

         require __DIR__ . '/../views/landing/index.php';
    }
}
