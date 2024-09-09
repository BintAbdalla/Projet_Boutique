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
    


    public function find($id) {}

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
        $client = Client::find($id);

        if (!$client) {
            throw new Exception('Client non trouvé.', 404);
        }

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


    protected function generateQRCodeForClient(Client $client): string
    {
        // $user = $client->user;
        $qrContent = "ID Client: " . $client->id . "\n" .   // Ajout de l'ID du client
            //  "Nom: " . ($user->nom ?? 'N/A') . "\n" .
            //  "Prénom: " . ($user->prenom ?? 'N/A') . "\n" .
            "Téléphone: " . ($client->telephone ?? 'N/A') . "\n" .
            "Surnom: " . ($client->surnom ?? 'N/A');

        // Définir le chemin du fichier QR code
        $qrcodepath = 'qrcodes/clients_' . $client->id . '.png';

        // Appeler le service pour générer le QR code
        $this->qrcode->generateQRCode($qrContent, $qrcodepath);

        return $qrcodepath; // Retourner le chemin du QR code
    }
    public function index(array $include = [])
    {


        try {
            // Utiliser QueryBuilder pour appliquer les filtres et les inclusions
            $query = QueryBuilder::for(Client::class)
                ->allowedFilters(['surname'])
                ->allowedIncludes(['user'])
                ->with($include)  // Inclure les relations spécifiées
                ->whereNotNull('user_id');  // Appliquer le filtre `whereNotNull`

            return $query->get();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
