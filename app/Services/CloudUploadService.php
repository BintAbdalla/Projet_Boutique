<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;


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
            // Log l'erreur
            Log::error('Erreur lors de l\'upload sur Cloudinary : ' . $e->getMessage());
            throw $e; // Propager l'erreur pour que le Job puisse la gérer
        }
    }
}
