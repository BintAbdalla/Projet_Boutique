<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Transformation\Transformation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CloudUploadService
{
    protected $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary();
    }

    /**
     * Télécharge un fichier sur Cloudinary et retourne son URL.
     *
     * @param  UploadedFile  $file
     * @return string
     */
    public function uploadAndGetUrl(UploadedFile $file): string
    {
        try {
            // Télécharger le fichier sur Cloudinary
            $upload = $this->cloudinary->uploadApi()->upload($file->getRealPath(), [
                'folder' => 'photos' // Optionnel : spécifie un dossier dans Cloudinary
            ]);

            // Retourner l'URL du fichier
            return $upload['secure_url'];
        } catch (\Exception $e) {
            // En cas d'échec, stocker localement
            return $this->storeLocally($file);
        }
    }

    /**
     * Stocke le fichier localement et retourne son URL.
     *
     * @param  UploadedFile  $file
     * @return string
     */
    protected function storeLocally(UploadedFile $file): string
    {
        // Nom du fichier
        $fileName = time() . '.' . $file->getClientOriginalExtension();

        // Stockage du fichier
        $path = $file->storeAs('photos', $fileName, 'public');

        // Retourner l'URL du fichier local
        return Storage::url($path);
    }
}
