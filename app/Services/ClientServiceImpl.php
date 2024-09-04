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

class ClientServiceImpl implements ClientService
{
    protected $repo;

    public function __construct(ClientRepository $repo)
    {
        $this->repo = $repo;
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

    public function store($clientData, $userData = null)
    { {
            try {

                DB::beginTransaction();

                // Création du client
                $client = Client::create($clientData);

                if ($userData) {
                    // Assurez-vous que les données utilisateur sont présentes
                    if (!isset($userData['role'])) {
                        $userData['role'] = 'client'; // Valeur par défaut
                    }

                    $roledefault = Role::where("role", $userData['role'])->first();
                    if (!$roledefault) {
                        throw new Exception('Role not found: ' . $userData['role']);
                    }

                    $user = User::create([
                        'nom' => $userData['nom'] ?? '',
                        'prenom' => $userData['prenom'] ?? '',
                        'login' => $userData['login'] ?? '',
                        'password' => isset($userData['password']) ? Hash::make($userData['password']) : '',
                        'role_id' => $roledefault->id,
                    ]);

                    $user->client()->save($client);
                }

                DB::commit();
                return $client;
            } catch (Exception $e) {
                DB::rollBack();
                throw $e;
            }
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
    { {
            $client = Client::find($id);

            if (!$client) {
                throw new Exception('Client non trouvé.', 404);
            }

            return $client;
        }
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
}
