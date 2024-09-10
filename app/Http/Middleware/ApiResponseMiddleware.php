<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Enums\StateEnums;
use App\Traits\Responsetrait;
use Illuminate\Http\JsonResponse;
use App\Exceptions\ExceptionServiceDettes; // Import de l'exception service
use App\Exceptions\ExceptionRepositoryDettes; // Import de l'exception repository

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
        try {
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

        } catch (ExceptionServiceDettes $e) {
            // Interception des exceptions liées au service dettes
            return $this->sendResponse([], StateEnums::ECHEC, $e->getMessage(), Response::HTTP_BAD_REQUEST);

        } catch (ExceptionRepositoryDettes $e) {
            // Interception des exceptions liées au repository dettes
            return $this->sendResponse([], StateEnums::ECHEC, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);

        } catch (\Exception $e) {
            // Gestion générale des autres exceptions
            return $this->sendResponse([], StateEnums::ECHEC, 'Une erreur interne est survenue.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    protected function determineStatus(int $statusCode): string
    {
        // Retourne 'ECHEC' pour les erreurs (400+) et 'SUCCESS' pour les succès (200-299)
        return $statusCode >= 400 ? StateEnums::ECHEC->value : StateEnums::SUCCESS->value;
    }

    protected function getErrorMessage(int $statusCode): string
    {
        $messages = [
            400 => 'Mauvaise requête.',
            401 => 'Non autorisé.',
            403 => 'Interdit.',
            404 => 'Non trouvé.',
            405 => 'Méthode non autorisée.',
            422 => 'Entité non traitable.',
            500 => 'Erreur interne du serveur.',
            502 => 'Mauvaise passerelle.',
            503 => 'Service indisponible.',
        ];

        return $messages[$statusCode] ?? 'Une erreur inconnue est survenue.';
    }
}
