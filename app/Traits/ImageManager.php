<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager as ImageProcessor;
use Intervention\Image\Drivers\Gd\Driver;

trait ImageManager
{
    public function uploadAndCompress($file, $folder, $name)
    {
        // 1. Buat nama file unik dengan ekstensi .webp
        $filename = time() . '_' . str_replace(' ', '_', $name) . '.webp';
        
        // 2. Tentukan Path: storage/app/public/uploads/venues atau uploads/fields
        $subFolder = 'uploads/' . $folder;
        $path = storage_path('app/public/' . $subFolder);

        // 3. Buat folder jika belum ada
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        // 4. Inisialisasi Manager dengan Driver GD (Aman untuk XAMPP)
        $manager = new ImageProcessor(new Driver());
        
        // 5. Proses Gambar: Baca -> Resize/Cover -> Encode ke WebP -> Simpan
        $manager->read($file)
            ->cover(1000, 700) // Ukuran standar yang cukup tajam tapi ringan
            ->toWebp(80)       // Konversi ke WebP dengan kualitas 80% (Sweet Spot Kompresi)
            ->save($path . '/' . $filename);

        // 6. Return path untuk disimpan ke Database
        return $subFolder . '/' . $filename;
    }
}