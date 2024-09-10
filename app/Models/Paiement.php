<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    use HasFactory;

    protected $table = 'paiements';

    protected $fillable = [
        'dette_id',
        'montant',
        'date_paiement',
    ];

    // Relation avec la dette
    public function dette()
    {
        return $this->belongsTo(Dettes::class, 'dette_id');
    }
}
