<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\TelephoneScope;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'surname', 'telephone', 'adresse', 'user_id', 'qr_code'
    ];

    protected $hidden = [
        // Champs cachés, si nécessaire
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $guarded = [
        // Attributs protégés contre l'assignation massive
    ];

    protected static function booted()
    {
        static::addGlobalScope(new TelephoneScope);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function dettes()
    {
        return $this->hasMany(Dettes::class, 'client_id');
    }
}
