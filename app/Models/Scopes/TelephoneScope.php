<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TelephoneScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Récupérer le filtre 'telephone' de la requête
        $telephone = request('telephone');

        // Appliquer le filtre sur le champ 'telephone' si présent
        if (!empty($telephone)) {
            $builder->where('telephone', 'LIKE', '%' . $telephone . '%');
        }
    }
}