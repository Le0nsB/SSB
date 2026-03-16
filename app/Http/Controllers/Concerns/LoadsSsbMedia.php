<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Support\Facades\File;

trait LoadsSsbMedia
{
    protected function getSsbMediaItems(): \Illuminate\Support\Collection
    {
        $clipDirectory = public_path('media/ssb-clips');

        if (! File::isDirectory($clipDirectory)) {
            return collect();
        }

        $videoExtensions = ['mp4', 'webm', 'mov', 'm4v'];
        $imageExtensions = ['jpg', 'jpeg', 'png', 'webp'];

        return collect(File::files($clipDirectory))
            ->filter(function ($file) use ($videoExtensions, $imageExtensions) {
                $extension = strtolower($file->getExtension());
                $filename = strtolower($file->getFilenameWithoutExtension());

                if ($filename === 'ssb') {
                    return false;
                }

                return in_array($extension, $videoExtensions, true)
                    || in_array($extension, $imageExtensions, true);
            })
            ->sortByDesc(fn ($file) => $file->getMTime())
            ->values()
            ->map(function ($file) use ($videoExtensions) {
                $extension = strtolower($file->getExtension());
                $isVideo = in_array($extension, $videoExtensions, true);

                return [
                    'name' => $file->getFilenameWithoutExtension(),
                    'url' => asset('media/ssb-clips/'.$file->getFilename()),
                    'kind' => $isVideo ? 'video' : 'image',
                    'type' => $isVideo ? 'video/'.$extension : 'image/'.$extension,
                ];
            });
    }
}
