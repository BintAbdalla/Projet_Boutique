<?php

// app/Services/MailService.php

namespace App\Services;

use App\Jobs\EmailJob;
use App\Models\User;

class MailService
{
    /**
     * Envoyer un e-mail en utilisant un job.
     *
     * @param array $data
     * @param string $recipient
     * @param string $pdfPath
     * @return void
     */
    public function sendEmail(array $data, $recipient, $pdfPath)
    {
        // Trouver l'utilisateur ou créer un modèle utilisateur basé sur les données fournies
        $user = User::where('login', $recipient)->first();

        if ($user) {
            // Dispatche le job pour envoyer l'e-mail
            EmailJob::dispatch($user);
        } else {
            // Gérer le cas où l'utilisateur n'est pas trouvé
            // Par exemple, vous pouvez lancer une exception ou enregistrer une erreur
            throw new \Exception("Utilisateur non trouvé pour l'email: $recipient");
        }
    }
}
