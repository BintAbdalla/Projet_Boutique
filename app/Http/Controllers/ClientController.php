<?php

namespace App\Http\Controllers;

use App\Enums\StateEnums;
use App\Http\Requests\StoreClientRequests;
use App\Http\Resources\ClientCollection;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Models\User;
use App\Traits\Responsetrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Exception;
use Prettus\Validator\Exceptions\ValidatorException;
use Spatie\QueryBuilder\QueryBuilder;
use App\Models\Role; // Importez le modèle Role
use App\Models\Dettes;
use App\Services\ClientService;
use App\Exceptions\ExceptionService;



class ClientController extends Controller
{
    use Responsetrait;
    //     /**
    //      * Display a listing of the resource.
    //      */
    protected $clientService;

    protected $exceptionService;

    public function __construct(ClientService $clientService, ExceptionService $exceptionService)
    {
        $this->clientService = $clientService;
        $this->exceptionService = $exceptionService;
    }

    public function index(Request $request)
    {
        try {
            $include = $request->has('include') ? [$request->input('include')] : [];
            $clients = $this->clientService->index($include);

            // Retourner une réponse qui sera formatée par le middleware
            return $this->sendResponse($clients, StateEnums::SUCCESS, 'Clients récupérés avec succès');
        } catch (ExceptionService $e) {
            // Retourner une réponse d'erreur qui sera formatée par le middleware
            $formattedError = [
                'error' => $this->exceptionService->handleException($e),
            ];

            // Retourner une réponse qui sera formatée par le middleware
            return $this->sendResponse($formattedError, StateEnums::ECHEC, 'Une erreur est survenue', 500);
        }
    }

    public function store(StoreClientRequests $request)

    {
        // $this->authorize('create', Client::class);

        try {
            $clientData = $request->only('surname', 'adresse', 'telephone');
            $userData = $request->has('users') ? $request->input('users') : null;

            // Ajouter le rôle du client à clientData si nécessaire
            if ($userData && isset($userData['role'])) {
                $userData['role'];
            }

            $client = $this->clientService->store($clientData, $userData);

            return $this->sendResponse(new ClientResource($client), StateEnums::SUCCESS, 'Client créé avec succès');
        } catch (ExceptionService $e) {
            // Utiliser le service d'exception pour gérer l'erreur
            $formattedError = $this->exceptionService->handleException($e);

            // Retourner une réponse formatée
            return $this->sendResponse($formattedError, StateEnums::ECHEC);
        }
    }

    public function filterByTelephone(Request $request)
    {
        try {
            $telephone = $request->input('telephone');
            $clients = $this->clientService->filterByTelephone($telephone);

            return $this->sendResponse($clients, StateEnums::SUCCESS, 'Clients récupérés avec succès.');
        } catch (ExceptionService $e) {
            return $this->sendResponse(['error' => $e->getMessage()], StateEnums::ECHEC, 'error', $e->getCode());
        }
    }



    public function show($id, Request $request)
    {
        try {
            $client = $this->clientService->show($id);
            return $this->sendResponse($client, StateEnums::SUCCESS, 'Client récupéré avec succès.');
        } catch (ExceptionService $e) {
            return $this->sendResponse(['error' => $e->getMessage()], StateEnums::ECHEC, 'error', $e->getCode());
        }
    }



    public function getUserForClient($id)
    {
        try {
            $clientData = $this->clientService->getUserForClient($id);
            return $this->sendResponse($clientData);
        } catch (ExceptionService $e) {
            return $this->sendResponse(['error' => $e->getMessage()], $e->getCode());
        }
    }

    public function getDettes(Request $request, $id)
    {
        try {
            $detteData = $this->clientService->getDettes($id);
            return $this->sendResponse($detteData,StateEnums::SUCCESS);
        } catch (Exception $e) {
            return $this->sendResponse(['error' => $e->getMessage()], StateEnums::ECHEC, $e->getCode());
        }
    }
}
