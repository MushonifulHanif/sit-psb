<?php

class FileUpload {
    private $directory;
    private $allowedExtensions;
    private $maxSize; // in bytes

    public function __construct($directory, $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'], $maxSize = 10485760) {
        $this->directory = __DIR__ . '/../' . $directory;
        $this->allowedExtensions = $allowedExtensions;
        $this->maxSize = $maxSize;
        
        if (!is_dir($this->directory)) {
            mkdir($this->directory, 0755, true);
        }
    }

    public function upload($fileInputName) {
        if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] !== UPLOAD_ERR_OK) {
             return ['error' => 'Tidak ada file yang diunggah atau terjadi kesalahan upload.'];
        }

        $fileInfo = pathinfo($_FILES[$fileInputName]['name']);
        $extension = strtolower($fileInfo['extension'] ?? '');

        if (!in_array($extension, $this->allowedExtensions)) {
            return ['error' => 'Ekstensi file tidak diizinkan. Hanya: ' . implode(', ', $this->allowedExtensions)];
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES[$fileInputName]['tmp_name']);
        finfo_close($finfo);

        $allowedMimes = [
            'image/jpeg', 'image/png', 'image/jpg', 'application/pdf', 
            'audio/webm', 'audio/ogg', 'audio/mp3', 'audio/mpeg'
        ];
        
        if (!in_array($mime, $allowedMimes)) {
            return ['error' => 'Tipe file (MIME) tidak valid.'];
        }

        if ($_FILES[$fileInputName]['size'] > $this->maxSize) {
             return ['error' => 'Ukuran file terlalu besar. Maksimal ' . ($this->maxSize / 1048576) . ' MB.'];
        }

        // Generate unique name
        $newFilename = uniqid('file_', true) . '.' . $extension;
        $destination = rtrim($this->directory, '/') . '/' . $newFilename;

        if (move_uploaded_file($_FILES[$fileInputName]['tmp_name'], $destination)) {
            // Compress image if it is an image
            if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
                $this->compressImage($destination, $destination, 70);
            }
            return ['success' => true, 'filename' => $newFilename];
        }

        return ['error' => 'Gagal memindahkan file ke direktori tujuan.'];
    }

    private function compressImage($source, $destination, $quality) {
        $info = getimagesize($source);
        if (!$info) return;

        if ($info['mime'] == 'image/jpeg') {
            $image = imagecreatefromjpeg($source);
            imagejpeg($image, $destination, $quality);
        } elseif ($info['mime'] == 'image/png') {
            $image = imagecreatefrompng($source);
            imagealphablending($image, false);
            imagesavealpha($image, true);
            // quality for imagepng is 0-9
            $pngQuality = 9 - round(($quality * 9) / 100);
            imagepng($image, $destination, $pngQuality);
        }
        
        if (isset($image)) {
            imagedestroy($image);
        }
    }
}
