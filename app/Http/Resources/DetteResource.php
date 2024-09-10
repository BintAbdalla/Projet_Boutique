<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;



class DetteResource extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(function ($dette) {
            return [
                'id' => $dette->id,
                'montant' => $dette->montant,
                'clientId' => $dette->client_id, 
                'paiments' => $dette->paiments 
            ];
        })->all();
    }
}


