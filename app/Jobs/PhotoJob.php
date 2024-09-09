<?php

namespace App\Jobs;

use App\Services\CloudUploadService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;

class PhotoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $userId;

    /**
     * Crée une nouvelle instance de Job.
     *
     * @param string $filePath
     * @param int $userId
     */
    public function __construct(string $filePath, int $userId)
    {
        $this->filePath = $filePath;
        $this->userId = $userId;
    }

    /**
     * Exécute le job.
     *
     * @param CloudUploadService $cloudUploadService
     * @return void
     */
    public function handle(CloudUploadService $cloudUploadService)
    {
        // Lire le contenu du fichier depuis le disque local
        $fileContent = Storage::disk('public')->get($this->filePath);

        // Créer un objet UploadedFile temporaire à partir du contenu du fichier
        $tempFilePath = tempnam(sys_get_temp_dir(), 'upload');
        file_put_contents($tempFilePath, $fileContent);
        $file = new UploadedFile(
            $tempFilePath,
            basename($this->filePath),
            mime_content_type($tempFilePath), // Utiliser la fonction pour obtenir le type MIME
            null,
            true
        );

        // Utiliser le service CloudUploadService pour uploader la photo
        try {
            $photoUrl = $cloudUploadService->uploadAndGetUrl($file);

            // Mettre à jour l'utilisateur avec l'URL de la photo
            $user = User::find($this->userId);
            if ($user) {
                $user->update([
                    'filename' => $photoUrl,
                ]);
            }
        } catch (\Exception $e) {
            // Log l'erreur
            Log::error('Erreur lors de l\'upload de la photo : ' . $e->getMessage());
        }

        // Nettoyer le fichier temporaire
        unlink($tempFilePath);

        // Supprimer le fichier temporaire stocké localement
        Storage::disk('public')->delete($this->filePath);
    }
}
