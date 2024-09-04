<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Article extends Model
{
    use HasFactory,SoftDeletes;


    // Indique la table associée au modèle (optionnel si le nom de la table suit la convention Laravel)
    protected $table = 'articles';

    // Définit les attributs qui peuvent être assignés en masse
    protected $fillable = [
        'libelle',
        'prix',
        'qteStock',
    ];
// Définit les attributs qui doivent être castés en types natifs
    protected $casts = [
        'prix' => 'decimal:2',
        'qteStock' => 'integer',
    ];

    protected $hidden =[
        'created_at',
        'updated_at',
    ];
    
    protected $dates = ['deleted_at']; // Ajoute la date de suppression à la liste des dates



 // Ajout des scopes globaux pour filtrer par libelle et disponibilité
 /// Ajout des scopes globaux pour filtrer par libelle et disponibilité
    protected static function booted()
    {
        static::addGlobalScope('filter', function (Builder $builder) {
            // Filtrer par libelle si présent dans la requête
            if (request()->has('libelle')) {
                $builder->where('libelle', request()->input('libelle'));
            }

            // Filtrer par disponibilité si présent dans la requête
            if (request()->has('disponible')) {
                $disponible = request()->input('disponible');
                $builder->when($disponible === 'oui', function ($query) {
                    $query->where('qteStock', '>', 0);
                })->when($disponible === 'non', function ($query) {
                    $query->where('qteStock', '<=', 0);
                });
            }
        });
    }

}