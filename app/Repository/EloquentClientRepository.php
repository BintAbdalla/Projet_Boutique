<?php

namespace App\Repository;

use App\Models\Client;


class EloquentClientRepository implements ClientRepository
{
    public function index(array $include = [])
    {
        // Inclure les relations spécifiées et appliquer le filtre whereNotNull
        return Client::with($include)->whereNotNull('user_id')->get();
    }

    public function store($clientData, $userData = null)
    {
        // Créer un nouveau client avec les données fournies
        return Client::create($clientData, $userData = null);
    }

    public function find($id)
    {
        // Trouver un client par ID
        return Client::find($id);
    }

    public function update($id, array $data)
    {
        // Trouver le client par ID et mettre à jour ses informations
        $client = $this->find($id);
        $client->update($data);
        return $client;
    }

    public function delete($id)
    {
        // Trouver le client par ID et le supprimer
        $client = $this->find($id);
        return $client->delete();
    }

    public function filterByTelephone(string $telephone)
    {
        // Filtrer les clients par numéro de téléphone
        return Client::where('telephone', $telephone)->get();
    }

    public function getDettes($clientId)
    {
        // Récupérer les dettes associées au client
        return Client::find($clientId)->dettes;
    }

    public function show($id)
    {
        // Trouver et retourner un client par ID
        return Client::find($id);
    }

    public function getUserForClient($id)
    {
        // Récupérer l'utilisateur associé au client
        $client = Client::with('user')->find($id);
        return $client ? $client->user : null;
    }
}
