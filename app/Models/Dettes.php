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
        'client_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    // Relation avec les articles
    public function articles()
    {
        return $this->belongsToMany(Article::class, 'article_dettes', 'id_dette', 'id_article')
            ->withPivot('qteVente', 'prixVente')
            ->withTimestamps();
    }

    // Relation avec le client
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Relation avec les paiements
    public function paiements()
    {
        return $this->hasMany(Paiement::class, 'dette_id'); // Assurez-vous que le nom de la colonne est correct
    }

    public function getMontantVerserAttribute(): float
    {
        return $this->paiements->sum('montant');
    }

    public function getMontantRestantAttribute(): float
    {
        return $this->montant - $this->montantVerser;
    }
}
