<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Enums\UserRole; // Assurez-vous que l'énumération est importée
// use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use App\Jobs\UploadJob;
// use Laravel\Passport\HasApiTokens;
// use Tymon\JWTAuth\Contracts\JWTSubject;


// use App\Models\User;




class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
    use HasFactory;
    // $user = User::find(1); // ou tout autre initialisation de l'utilisateur

    // if ($user) {
    //     UploadJobJob::dispatch($user);
    // }
    /**
     * 
     * 
     * 
     * 
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array
     */
    protected $fillable = [
        'nom',
        'prenom',
        'login',
        'password',
        'role_id',
        'filename'
        
    ];

    /**
     * Les attributs qui doivent être cachés pour les tableaux et JSON.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'create_at',
        'update_at',
    ];

    /**
     * Les attributs à convertir en types natifs.
     *
     * @var array
     */
    protected $casts = [
        'created_at',
        'updated_at',
        
    ];
    
    /**
     * Les attributs qui ne peuvent pas être assignés en masse.
     *
     * @var array
     */
    protected $guarded = [
        ['id']
    ];
    
    
    /**
     * Les attributs qui doivent être traités comme des dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        
    ];
    
    public function client()
    {
        return $this->hasOne(Client::class, 'user_id');
    }
    
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    
}
