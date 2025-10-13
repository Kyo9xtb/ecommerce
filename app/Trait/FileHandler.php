<?php

namespace App\Trait;

use Illuminate\Support\Facades\Storage;

trait FileHandler
{
    protected string $disk = 'public';

    protected function processFiles($files, string $baseDir = 'uploads'): array
    {
        if (empty($files)) return [];
        $images = [];
        $storage = Storage::disk($this->disk ?? 'public');

        $files = is_array($files) ? $files : [$files];

        foreach ($files as $file) {
            $fileName = date('Ymd_His') . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $storage->putFileAs($baseDir, $file, $fileName);
            $images[] = $fileName;
        }
        return $images;
    }

    protected function moveFiles(string | array $filenames, string $fromDir, string $toDir): void
    {
        $disk = Storage::disk($this->disk ?? 'public');
        $filenames = (array)$filenames;
        foreach ($filenames as $name) {
            if ($disk->exists("$fromDir/$name")) {
                $disk->move("$fromDir/$name", "$toDir/$name");
            }
        }
    }

    protected function deleteFiles(array | string $paths)
    {
        $disk = Storage::disk($this->disk ?? 'public');
        $disk->delete((array) $paths);
    }

    protected function deleteFolders(array|string $paths): void
    {
        $disk = Storage::disk($this->disk ?? 'public');

        $paths = is_array($paths) ? $paths : [$paths];
        foreach ($paths as $path) {
            if ($disk->exists($path)) {
                $disk->deleteDirectory($path);
            }
        }
    }
}
