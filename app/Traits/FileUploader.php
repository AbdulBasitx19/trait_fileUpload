<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait FileUploader
{
    public function uploadFile(UploadedFile $file , string $folder, string $disk = 'public') : array 
    {
        $uniqueName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($folder, $uniqueName, $disk);

        return [
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ];
    }

    public function deleteFile(string $path , string $disk = 'public'): bool 
    {
        return Storage::disk($disk)->delete($path);
    }

    public function formatFileSize(int $bytes): string 
    {
        if($bytes >= 1048576){
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        
        return number_format($bytes / 1024, 2) . ' KB';
    }
}