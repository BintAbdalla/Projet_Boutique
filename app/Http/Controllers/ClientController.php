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
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use SimpleSoftwareIO\QrCode\Exceptions\QrCodeException;
use App\Services\QrCodeService;
use App\Services\CloudUploadService;
use Illuminate\Http\UploadedFile;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\carteFidelitéServices;
use Illuminate\Http\Response;
use App\Mail\ExampleMail;
use Illuminate\Support\Facades\Mail;
use App\Services\MailService;




class ClientController extends Controller
{
    use Responsetrait;
    //     /**
    //      * Display a listing of the resource.
    //      */
    protected $clientService;

    protected $exceptionService;
    protected $qrCodeService;
    protected $carteFidelitéServices;

    protected $mail;
    public function __construct(ClientService $clientService, ExceptionService $exceptionService, QrCodeService $qrCodeService, carteFidelitéServices $carteFidelitéServices, MailService $mailService)
    {
        $this->clientService = $clientService;
        $this->exceptionService = $exceptionService;
        $this->qrCodeService = $qrCodeService;
        $this->carteFidelitéServices = $carteFidelitéServices;
        $this->mail = $mailService;
    }


    public function store(StoreClientRequests $request)
    {
        try {
            // Extraire les données du client et de l'utilisateur
            $clientData = $request->only('surname', 'adresse', 'telephone');
            $userData = $request->only('users.nom', 'users.prenom', 'users.login', 'users.password', 'users.role', 'users.filename');

            // Création du client via le service
            $client = $this->clientService->store($clientData, $userData);
        } catch (Exception $e) {
            // Utiliser le service d'exception pour gérer l'erreur
            $formattedError = $this->exceptionService->handleException($e);

            // Retourner une réponse formatée avec l'erreur
            return response()->json([
                'status' => 'error',
                'message' => $formattedError
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    
        public function index(Request $request)
        {
            try {
                // Récupérer les filtres depuis la requête
                $filters = $request->only(['surname', 'id', 'user_id']); // Extraire les filtres de la requête
                
                // Vérifier si un paramètre 'include' est fourni
                $include = $request->has('include') ? explode(',', $request->input('include')) : [];
                
                // Inclusions autorisées
                $allowedIncludes = ['user'];
                $include = array_intersect($include, $allowedIncludes); // Filtrer les inclusions non autorisées
                
                // Récupérer les clients via le service avec les filtres et les inclusions
                $clients = $this->clientService->index($include, $filters);
                // dd($clients);
        
                // Retourner une réponse formatée
                return $this->sendResponse($clients, StateEnums::SUCCESS, 'Clients récupérés avec succès');
            } catch (ExceptionService $e) {
                // Gestion des exceptions via le service dédié
                $formattedError = [
                    'error' => $this->exceptionService->handleException($e),
                ];
        
                return $this->sendResponse($formattedError, StateEnums::ECHEC, 'Une erreur est survenue', 500);
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
            // Appeler le service pour obtenir le client par ID
            $client = $this->clientService->show($id);
    
            // Retourner une réponse formatée
            return $this->sendResponse($client, StateEnums::SUCCESS, 'Client récupéré avec succès.');
        } catch (Exception $e) {
            // Retourner une réponse d'erreur formatée
            return $this->sendResponse(['error' => $e->getMessage()], StateEnums::ECHEC, 'Une erreur est survenue.', $e->getCode());
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
            return $this->sendResponse($detteData, StateEnums::SUCCESS);
        } catch (Exception $e) {
            return $this->sendResponse(['error' => $e->getMessage()], StateEnums::ECHEC, $e->getCode());
        }
    }
    
}
