<?php

namespace App\Services;

use App\Repository\ClientRepository;
use App\Models\Client;
use App\Http\Resources\ClientResource;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;
use Exception;
use App\Models\Role; // Importez le modèle Role
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Dettes;
use App\Services\QrCodeService;
use App\Services\CloudUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Services\carteFidelitéServices;
use Barryvdh\DomPDF\Facade\Pdf; // Assurez-vous d'inclure ce namespace
use App\Mail\ExampleMail;
use App\Services\MailService;
use App\Jobs\EmailJob;
use App\Exceptions\ExceptionService;



class ClientServiceImpl implements ClientService
{
    protected $repo;
    protected $qrcode;

    protected $card;

    protected $mail;

    public function __construct(ClientRepository $repo, QrCodeService $qrcode, carteFidelitéServices $card, MailService $mail)
    {
        $this->repo = $repo;
        $this->qrcode = $qrcode;
        $this->card = $card;
        $this->mail = $mail;

        // $this->cloudUpload = new CloudUploadService();
    }


    public function store($clientData, $userData = null)
    {
        try {
            DB::beginTransaction();

            // Création du client
            $client = Client::create($clientData);

            if ($userData) {
                // Vérifier que 'users' existe dans $userData
                if (!isset($userData['users'])) {
                    throw new Exception('Les données utilisateur sont manquantes.');
                }

                $userData = $userData['users'];

                // Définir le rôle de l'utilisateur
                $userData['role'] = $userData['role'] ?? 'client';
                $roledefault = Role::where("role", $userData['role'])->first();
                if (!$roledefault) {
                    throw new Exception('Role not found: ' . $userData['role']);
                }

                $cloudUploadService = new CloudUploadService();
                $filenameUrl = null;

                // Vérifier si 'filename' est bien un fichier
                if (isset($userData['filename']) && $userData['filename'] instanceof UploadedFile) {
                    $filenameUrl = $cloudUploadService->uploadAndGetUrl($userData['filename']);
                }

                $user = User::create([
                    'nom' => $userData['nom'] ?? '',
                    'prenom' => $userData['prenom'] ?? '',
                    'login' => $userData['login'] ?? '',
                    'password' => isset($userData['password']) ? Hash::make($userData['password']) : '',
                    'role_id' => $roledefault->id,
                    'filename' => $filenameUrl,
                ]);

                // Associer le client à l'utilisateur
                $client->update(['user_id' => $user->id]);
                $client->save();

                // Génération du QR code
                $qrCodePath = 'qrcodes/' . $client->surname . '_' . $client->id . '.png';
                $this->qrcode->generateQRCode(json_encode($client), $qrCodePath);

                // Génération de la carte de fidélité
                $fidelityCardPath = $this->card->generateFidelityCardForClient($client, $qrCodePath, $filenameUrl);

                // Dispatcher le job pour envoyer l'email
                dispatch(new EmailJob($user, $fidelityCardPath));
            }

            DB::commit();
            return $client;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }



    public function find($id)
    { {
            try {
                // Récupérer tous les clients
                $clients = Client::all();

                return $clients;
            } catch (Exception $e) {
                throw new Exception('Erreur lors de la récupération des clients : ' . $e->getMessage());
            }
        }
    }

    public function update($id, array $data) {}

    public function delete($id) {}

    public function filterByTelephone($telephone)
    {
        if (empty($telephone)) {
            throw new Exception('Le numéro de téléphone est requis.', 422);
        }

        // Rechercher les clients par numéro de téléphone
        $clients = DB::table('clients')
            ->where('telephone', $telephone)
            ->get();

        if ($clients->isEmpty()) {
            throw new Exception('Aucun client trouvé pour ce numéro de téléphone.', 404);
        }

        return $clients;
    }

    public function getDettes($clientId)
    {
        // Récupérer les dettes du client avec l'ID donné
        $dettes = Dettes::where('client_id', $clientId)->get();

        // Vérifiez si des dettes existent pour le client
        if ($dettes->isEmpty()) {
            throw new Exception('Aucune dette trouvée pour ce client.', 404);
        }

        return [
            'client_id' => $clientId,
            'dettes' => $dettes
        ];
    }
    public function show($id)
    {
        // Valider l'ID pour s'assurer qu'il est un entier positif
        if (!is_numeric($id) || $id <= 0) {
            throw new Exception('ID invalide.', 400);
        }

        // Récupérer le client par ID
        $client = Client::find($id);

        // Vérifier si le client a été trouvé
        if (!$client) {
            throw new Exception('Client non trouvé.', 404);
        }

        // Retourner les détails du client
        return $client;
    }



    public function getUserForClient($id)
    {
        // Récupérer le client avec l'utilisateur associé
        $client = Client::with('user')->find($id);

        // Vérifiez si le client existe
        if (!$client) {
            throw new Exception('Client non trouvé.', 404);
        }

        // Vérifiez si l'utilisateur est associé
        if (!$client->user) {
            throw new Exception('Aucun utilisateur associé à ce client.', 404);
        }

        return [
            'client_id' => $client->id,
            'user' => $client->user
        ];
    }


    public function index(array $include = [], array $filters = [])
    {
        try {
            // Construire la requête avec QueryBuilder
            $query = QueryBuilder::for(Client::class)
                ->allowedFilters(['surname', 'id', 'user_id']) // Ajouter les filtres autorisés
                ->allowedIncludes(['user'])  // Relations à inclure
                ->with($include)             // Inclure les relations spécifiées
                ->whereNotNull('user_id');  // Filtrer sur `user_id`

            // Appliquer les filtres spécifiés
            foreach ($filters as $filter => $value) {
                $query->where($filter, $value);
            }

            // Récupérer les clients
            return $query->get();
        } catch (Exception $e) {
            throw new ExceptionService($e->getMessage());
        }
    }
}
