<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Role;

class UserPolicy
{
    /**
     * Determine if the given user can view the user.
     */
    public function view(User $authUser, User $user): bool
    {
        // Exemple : Un utilisateur peut voir les détails d'un autre utilisateur seulement s'il est admin
        return $authUser->role->name === 'admin' || $authUser->role->name ==='boutiquier';
    }

    /**
     * Determine if the given user can update the user.
     */
    public function update(User $authUser, User $user): bool
    {
        // Exemple : Un utilisateur peut mettre à jour son propre profil ou si c'est un admin
        return $authUser->id === $user->id || $authUser->role->name === 'admin' || $authUser->role->name === 'Boutiquier';
    }

    /**
     * Determine if the given user can delete the user.
     */
    public function delete(User $authUser, User $user): bool
    {
        // Exemple : Un utilisateur peut supprimer un autre utilisateur seulement s'il est admin
        return $authUser->role->name === 'admin' || $authUser->role->name ==='boutiquier';
    }

    }

