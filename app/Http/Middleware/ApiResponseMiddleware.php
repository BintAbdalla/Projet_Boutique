<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Enums\StateEnums;
use App\Traits\ResponseTrait;
use Illuminate\Http\JsonResponse;


class ApiResponseMiddleware
{
    use Responsetrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Vérifiez si la réponse est une instance de JsonResponse
        if ($response instanceof JsonResponse) {
            $data = $response->getData(true);
            $status = $this->determineStatus($response->getStatusCode());

            // Ajouter des messages d'erreur ou de succès selon le statut déterminé
            $message = $this->getErrorMessage($response->getStatusCode());

            $response = $this->sendResponse($data, StateEnums::from($status), $message, $response->getStatusCode());
        }

        return $response;
    }
    // protected function determineStatus(int $statusCode): string
    protected function determineStatus(int $statusCode): string
    {
        // Retourne 'ECHEC' pour les erreurs (400+) et 'SUCCESS' pour les succès (200-299)
        return $statusCode >= 400 ? StateEnums::ECHEC->value : StateEnums::SUCCESS->value;
    }

    
    protected function getErrorMessage(int $statusCode): string
    {
        $messages = [
            400 => 'Mauvaise requête.',
            401 => 'Non autorisé .',
            403 => 'Interdit ',
            404 => 'Non trouvé .',
            405 => 'Méthode non autorisée ',
            422 => 'Entité non traitable ',
            500 => 'Erreur interne du serveur .',
            502 => 'Mauvaise passerelle.',
            503 => 'Service indisponible.',
        ];

        return $messages[$statusCode] ?? 'Une erreur inconnue est survenue';
    }
}
