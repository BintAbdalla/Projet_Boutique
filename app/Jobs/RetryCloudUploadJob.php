<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\CloudUploadService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class RetryCloudUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Exécuter le job pour relancer les uploads échoués.
     *
     * @param CloudUploadService $cloudUploadService
     * @return void
     */
    public function handle(CloudUploadService $cloudUploadService)
    {
        // Sélectionner les utilisateurs dont l'upload a échoué
        $users = User::where('is_uploaded_to_cloud', false)->get();

        foreach ($users as $user) {
            $filePath = $user->filename; // Supposons que la colonne 'filename' contient le chemin local

            if (Storage::disk('public')->exists($filePath)) {
                $fileContent = Storage::disk('public')->get($filePath);

                // Créer un fichier temporaire
                $tempFilePath = tempnam(sys_get_temp_dir(), 'upload');
                file_put_contents($tempFilePath, $fileContent);

                $file = new UploadedFile(
                    $tempFilePath,
                    basename($filePath),
                    mime_content_type($tempFilePath),
                    null,
                    true
                );

                // Essayer de relancer l'upload
                try {
                    $photoUrl = $cloudUploadService->uploadAndGetUrl($file);

                    // Mise à jour de l'utilisateur si l'upload réussit
                    $user->update([
                        'filename' => $photoUrl,
                        'is_uploaded_to_cloud' => true,
                    ]);

                    // Supprimer le fichier local une fois uploadé
                    Storage::disk('public')->delete($filePath);
                } catch (\Exception $e) {
                    Log::error('Erreur lors de la relance de l\'upload pour l\'utilisateur ' . $user->id . ' : ' . $e->getMessage());
                }

                // Nettoyer le fichier temporaire
                unlink($tempFilePath);
            }
        }
    }
}
