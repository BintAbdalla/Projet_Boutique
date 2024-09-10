<?php


namespace App\Http\Controllers;

use App\Services\DetteService;
use App\Http\Requests\StoreDetteRequests; // Utilisation du request de validation
use App\Exceptions\ExceptionServiceDettes; // Exception personnalisée

class DetteController extends Controller
{
    protected $detteService;

    // Injection du service DetteService via le constructeur
    public function __construct(DetteService $detteService)
    {
        $this->detteService = $detteService;
    }

    public function create(StoreDetteRequests $request)

    {
        try {
            // On délègue la logique métier au service
            $result = $this->detteService->create($request->validated());

            return response()->json([
                'success' => $result['validated_articles'],
                'errors' => $result['errors']
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Échec de l\'enregistrement de la dette',
                'details' => $e->getMessage()
            ], 500);
        }
}

}