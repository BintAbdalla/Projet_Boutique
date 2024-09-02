<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dettes extends Model
{
    use HasFactory;

    protected $table = 'dettes';

    protected $fillable = [
        'date',
        'montant',
        'montant_du',
        'montant_restant',
        'client_id',
    ];

    // Exemple de relation avec un modèle Article
    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    // Relation avec le client
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
