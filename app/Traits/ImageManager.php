<?php

namespace App\Traits;

use Intervention\Image\ImageManager as ImageLib;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

trait ImageManager {
    public function uploadAndCompress($file, $folder, $name)
    {
        // 1. Paksa ekstensi file menjadi .webp
        $filename = time() . '-' . Str::slug($name) . '.webp';
        $destinationPath = public_path('storage/' . $folder);

        if (!File::isDirectory($destinationPath)) {
            File::makeDirectory($destinationPath, 0777, true, true);
        }

        // Coba pakai GD, kalau gagal pakai Imagick
        try {
            $manager = new ImageLib(new GdDriver());
        } catch (\Exception $e) {
            $manager = new ImageLib(new ImagickDriver());
        }
        
        $img = $manager->read($file->getRealPath());
        
        // 2. Resize proposional (lebar 1000px)
        $img->scale(width: 1000); 

        // 3. Konversi ke WebP dan simpan dengan kualitas 80
        // Method toWebp() akan mengubah data image menjadi format webp sebelum di-save
        $img->toWebp(80)->save($destinationPath . '/' . $filename);

        return $folder . '/' . $filename;
    }
}