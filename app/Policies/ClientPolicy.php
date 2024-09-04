<?php

namespace App\Policies;

use App\Models\User;
use App\Enums\RoleEnums;
use App\Models\Client;

class ClientPolicy
{
    /**
     * Détermine si l'utilisateur peut voir le client.
     */
    public function view(User $user, Client $client): bool
    {
        // Vérifiez si l'utilisateur peut voir le client
        return $user->role->role === RoleEnums::BOUTIQUIER->value;
    }
    

    /**
     * Détermine si l'utilisateur peut créer un client.
     */
    public function create(User $user): bool
    {
        // $user->load('role');
        // dd($user->role->role);
        return $user->role->role  === RoleEnums::BOUTIQUIER->value;
    }

    /**
     * Détermine si l'utilisateur peut mettre à jour les informations d'un client.
     */
    public function update(User $user, Client $client): bool
    {
        return $user->role->role  === RoleEnums::BOUTIQUIER->value;
    }

    /**
     * Détermine si l'utilisateur peut supprimer un client.
     */
    public function delete(User $user, Client $client): bool
    {
        return $user->role->role  === RoleEnums::BOUTIQUIER->value;
    }
}
