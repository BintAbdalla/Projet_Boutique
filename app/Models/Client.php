<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    // Attributs que vous pouvez massivement assigner
    protected $fillable = [
        'surname', 'telephone', 'adresse','user_id'
    ];

    // Attributs cachés (non retournés par les méthodes toArray() et toJson())
    protected $hidden = [
        // Exemple : 'password' si vous aviez un mot de passe dans le modèle
    ];

    // Cast des attributs
    protected $casts = [
         'created_at' => 'datetime',
         'updated_at' => 'datetime',
    ];

    // Attributs non assignables en masse
    protected $guarded = [
        // Exemple : 'id' ou d'autres attributs que vous ne voulez pas assigner massivement
    ];

    
    public function user()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

  
}
