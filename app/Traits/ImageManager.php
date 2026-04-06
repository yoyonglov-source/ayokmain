<?php

namespace App\Traits;

use Intervention\Image\ImageManager as ImageProcessor;
use Intervention\Image\Drivers\Gd\Driver;

trait ImageManager
{
    public function uploadAndCompress($file, $folder, $name)
    {
        // 1. Nama file tetap .webp
        $filename = time() . '_' . str_replace(' ', '_', $name) . '.webp';
        
        $subFolder = 'uploads/' . $folder;
        $path = storage_path('app/public/' . $subFolder);

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $manager = new ImageProcessor(new Driver());
        
        // 2. Proses Eksekusi
        $manager->read($file)
            // Menggunakan cover agar UI Seragam (1000x700 adalah angka ideal)
            ->cover(800, 500) 
            // Kualitas 75% adalah sweet spot (File sangat kecil, mata manusia tak lihat bedanya)
            ->toWebp(75) 
            ->save($path . '/' . $filename);

        return $subFolder . '/' . $filename;
    }
}