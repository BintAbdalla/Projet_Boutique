<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}