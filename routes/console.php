<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Storage;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('fpm:clean-temp-uploads', function () {
    $disk = config('livewire.temporary_file_upload.disk') ?: config('filesystems.default');
    $directory = config('livewire.temporary_file_upload.directory') ?: 'livewire-tmp';

    $storage = Storage::disk($disk);
    $cutoff = now()->subDay();
    $removed = 0;

    foreach ($storage->files($directory) as $file) {
        $lastModified = $storage->lastModified($file);

        if ($lastModified !== false && now()->createFromTimestamp($lastModified)->lt($cutoff)) {
            $storage->delete($file);
            $removed++;
        }
    }

    $this->info("Removed {$removed} stale temporary upload file(s).");
})->purpose('Delete stale Livewire temporary upload files older than 1 day');

Schedule::command('fpm:clean-temp-uploads')->daily();
