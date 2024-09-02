<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dettes;

class DetteController extends Controller
{
    /**
     * Récupère les dettes pour un client spécifique sans détails supplémentaires.
     */
    public function getDettesForClient($client_id)
    {
        // Récupérer uniquement les dettes du client sans inclure les relations
        $dettes = Dettes::where('client_id', $client_id)->get();

        // Vérifiez si des dettes existent pour le client
        if ($dettes->isEmpty()) {
            return response()->json([
                'message' => 'Aucune dette trouvée pour ce client.'
            ], 404);
        }

        // Retourner les dettes sans détails supplémentaires dans une réponse JSON
        return response()->json([
            'client_id' => $client_id,
            'dettes' => $dettes
        ], 200);
    }
}
