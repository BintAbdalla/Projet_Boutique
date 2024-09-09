<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'login' => $this->login,
            'role' => $this->role->role ?? 'Non défini', // Exemple pour inclure une relation
            'etat' => $this->etat,
            'filename' => $this->filename ? asset('storage/photos/' . $this->filename) : null, // Formater le chemin de la photo
         
        ];
    }
}
