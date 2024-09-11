<?php


namespace App\Http\Controllers;

use App\Services\DetteService;
use App\Http\Requests\StoreDetteRequests; // Utilisation du request de validation
use App\Exceptions\ExceptionServiceDettes; // Exception personnalisée
use Illuminate\Http\JsonResponse; // Utilisation du retour de données en JSON
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StorePaiementRequests;



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

    public function listAll(): JsonResponse
    {
        // Utiliser le service pour récupérer toutes les dettes
        $dettes = $this->detteService->listAll();
        // dd($dettes);

        // Retourner les données au format JSON
        return response()->json([
            'data' => $dettes,
            'status' => 'SUCCESS',
            'message' => 'Liste des dettes récupérée avec succès.',
        ]);
    }



    public function listByClientId(int $clientId): JsonResponse
    {
        $dettes = $this->detteService->listByClientId($clientId);
        // dd($dettes); // Vérifiez le résultat ici
        return response()->json([
            'data' => $dettes,
            'status' => 'SUCCESS',
            'message' => 'Liste des dettes récupérée avec succès.',
        ]);
    }




    public function listByStatus(Request $request): JsonResponse
    {
        $statut = $request->query('statut'); // Récupère le paramètre 'statut'

        if (!$statut) {
            return response()->json([
                'data' => [],
                'status' => 'ERROR',
                'message' => 'Le paramètre statut est requis.',
            ]);
        }

        // Convertir le statut pour correspondre à la logique interne
        $statut = strtolower(trim($statut));

        if ($statut !== 'soldee' && $statut !== 'non_soldée') {
            return response()->json([
                'data' => [],
                'status' => 'ERROR',
                'message' => 'Le paramètre statut est invalide.',
            ]);
        }

        try {
            $dettes = $this->detteService->listByEtat($statut);

            // Vérifiez si $dettes est une collection et non null
            if (is_null($dettes)) {
                return response()->json([
                    'data' => [],
                    'status' => 'ERROR',
                    'message' => 'Erreur lors de la récupération des dettes.',
                ]);
            }

            // Ajouter des logs pour vérifier les données
            Log::info('Dettes Retrieved by Status:', $dettes->toArray());

            if ($dettes->isEmpty()) {
                return response()->json([
                    'data' => [],
                    'status' => 'SUCCESS',
                    'message' => 'Aucune dette trouvée pour ce statut.',
                ]);
            }

            return response()->json([
                'data' => $dettes,
                'status' => 'SUCCESS',
                'message' => 'Liste des dettes récupérée avec succès.',
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'data' => [],
                'status' => 'ERROR',
                'message' => $e->getMessage(),
            ]);
        }
    }




    public function getEtat(Request $request){ 
        $solde = $request->query('solde');
        // dd($solde);
        //Utiliser le service 
        $dettes = $this->detteService->getDettes($solde);
        // dd($etat); // Vérifiez le résultat ici

        return response()->json([
            'data' => $dettes,
            'status' => 'SUCCESS',
            'message' => 'État de la dette récupéré avec succès.',
        ]);
    }
        
    public function getDetteById($clientId)
    {
        // Utiliser le service pour obtenir les dettes du client
        $dettes = $this->detteService->getDetteById(clientId: $clientId);

        // Vérifier si des dettes existent pour ce client
        if ($dettes->isEmpty()) {
            return response()->json(['message' => 'Aucune dette trouvée pour ce client.'], 404);
        }

        // Retourner la liste des dettes du client
        return response()->json($dettes, 200);
    }

    public function getDetteDetailsByLibelle($detteId, $articleLibelle)
    {
        // Utiliser le service pour obtenir les détails de la dette et de l'article
        $dette = $this->detteService->getDetteDetailsByLibelle($detteId, $articleLibelle);

        // Vérifier si la dette ou l'article existe
        if (!$dette || $dette->articles->isEmpty()) {
            return response()->json(['message' => 'Dette ou article avec ce libellé non trouvée.'], 404);
        }

        // Retourner les détails de la dette et de l'article
        return response()->json($dette, 200);
    }


    public function listPaiementsByDette(Request $request, $detteId)
    {
        // Utiliser le service pour obtenir la dette avec ses paiements
        $dette = $this->detteService->getDetteByPaiement($detteId);

        // Vérifier si la dette existe
        if (!$dette) {
            return response()->json(['message' => 'Dette non trouvée.'], 404);
        }

        // Retourner l'objet dette avec les paiements associés
        return response()->json([
            'data' => [
                'dette' => $dette,
                'paiements' => $dette->paiements
            ]
        ], 200);
    }

    public function ajouterPaiement(StorePaiementRequests $request, int $id): JsonResponse
    {
        $result = $this->detteService->ajouterPaiement($id, $request->input('montant'));

        if ($result['status'] === 'error') {
            return response()->json([
                'data' => null,
                'message' => $result['message']
            ], 411);
        }

        return response()->json([
            'data' => $result['data'],
            'message' => $result['message']
        ], 200);
    }



}
