<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UploadService
{
    /**
     * Télécharge un fichier et retourne sa représentation en base64.
     *
     * @param  UploadedFile  $file
     * @return string
     */
    public function uploadAndGetBase64(UploadedFile $file): string
    {
        // Nom du fichier
        $fileName = time() . '.' . $file->getClientOriginalExtension();

        // Stockage du fichier
        $path = $file->storeAs('photos', $fileName, 'public');

        // Lire le contenu du fichier et convertir en base64
        $fileContent = Storage::disk('public')->get($path);
        return base64_encode($fileContent);
    }
}
