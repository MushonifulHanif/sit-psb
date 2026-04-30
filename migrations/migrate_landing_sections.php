<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Database.php';

$pdo = Database::getInstance()->getConnection();

echo "Memulai migrasi landing_sections...\n";

try {
    // 1. Create table
    $pdo->exec("CREATE TABLE IF NOT EXISTS landing_sections (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        tag VARCHAR(50) NOT NULL,
        content LONGTEXT,
        type ENUM('text', 'video') DEFAULT 'text',
        video_url VARCHAR(255) NULL,
        order_num INT DEFAULT 0,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Tabel 'landing_sections' berhasil dibuat.\n";

    // 2. Fetch data from pengaturan
    $stmt = $pdo->query("SELECT `key`, value FROM pengaturan WHERE `key` IN ('konten_informasi', 'konten_syarat', 'konten_biaya', 'url_tutorial_video')");
    $old_data = [];
    while ($row = $stmt->fetch()) {
        $old_data[$row['key']] = $row['value'];
    }

    // 3. Migrate to landing_sections
    $sections = [
        ['title' => 'Informasi Pendaftaran', 'tag' => 'Informasi', 'content' => $old_data['konten_informasi'] ?? '', 'type' => 'text', 'video_url' => '', 'order_num' => 1],
        ['title' => 'Syarat Pendaftaran', 'tag' => 'Syarat', 'content' => $old_data['konten_syarat'] ?? '', 'type' => 'text', 'video_url' => '', 'order_num' => 2],
        ['title' => 'Biaya Pendaftaran', 'tag' => 'Biaya', 'content' => $old_data['konten_biaya'] ?? '', 'type' => 'text', 'video_url' => '', 'order_num' => 3],
    ];

    // Add Video tutorial if exists
    if (!empty($old_data['url_tutorial_video'])) {
        $sections[] = [
            'title' => 'Tutorial Pendaftaran',
            'tag' => 'Tutorial',
            'content' => 'Tonton video panduan pendaftaran online pendaftar santri baru kami di bawah ini.',
            'type' => 'video',
            'video_url' => $old_data['url_tutorial_video'],
            'order_num' => 4
        ];
    }

    $stmtInsert = $pdo->prepare("INSERT INTO landing_sections (title, tag, content, type, video_url, order_num) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($sections as $s) {
        // Check if exists to avoid duplicates
        $check = $pdo->prepare("SELECT COUNT(*) FROM landing_sections WHERE tag = ?");
        $check->execute([$s['tag']]);
        if ($check->fetchColumn() == 0) {
            $stmtInsert->execute([$s['title'], $s['tag'], $s['content'], $s['type'], $s['video_url'], $s['order_num']]);
            echo "Section '{$s['tag']}' berhasil dimigrasi.\n";
        }
    }

    echo "Migrasi Selesai.\n";

} catch (PDOException $e) {
    echo "Gagal migrasi: " . $e->getMessage() . "\n";
}
