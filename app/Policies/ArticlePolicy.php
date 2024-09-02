<?php
namespace App\Policies;

use App\Models\User;
use App\Enums\RoleEnums;

class ArticlePolicy
{
    /**
     * Détermine si l'utilisateur peut voir l'article.
     */
    public function view(User $user): bool
    {
        return $user->role === RoleEnums::BOUTIQUIER->value;
    }

    /**
     * Détermine si l'utilisateur peut créer un article.
     */
    public function create(User $user): bool
    {
        return $user->role === RoleEnums::BOUTIQUIER->value;
    }

    /**
     * Détermine si l'utilisateur peut mettre à jour l'article.
     */
    public function update(User $user): bool
    {
        return $user->role === RoleEnums::BOUTIQUIER->value;
    }

    /**
     * Détermine si l'utilisateur peut supprimer l'article.
     */
    public function delete(User $user): bool
    {
        return $user->role === RoleEnums::BOUTIQUIER->value;
    }
}
