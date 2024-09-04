<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArticleRequests;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\Responsetrait;
use App\Enums\StateEnums;
use Exception;
use App\Services\ArticleService;

class ArticleController extends Controller
{
    use Responsetrait;

    protected $articleService;

    public function __construct(ArticleService $articleService)
    {
        // Injection du service ArticleService via le constructeur
        $this->articleService = $articleService;
    }

    /**
     * Store a newly created article in storage.
     *
     * @param StoreArticleRequests $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreArticleRequests $request)
    {
        // Autoriser la création de l'article
        try {
            $this->authorize('create', Article::class);
    
            // Valider les données (cela se fait automatiquement avec StoreArticleRequests)
            $validatedData = $request->validated();
    
            // Créer l'article en utilisant le service
            $articleResource = $this->articleService->create($validatedData);
    
            // Retourner une réponse avec les données de l'article créé
            return $this->sendResponse($articleResource, StateEnums::SUCCESS);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            // Retourner une réponse avec l'erreur d'autorisation
            return $this->sendResponse(['error' => 'Non autorisé'], StateEnums::ECHEC, 403);
        } catch (Exception $e) {
            // Retourner une réponse avec l'erreur
            return $this->sendResponse(['error' => $e->getMessage()], StateEnums::ECHEC, 500);
        }
    }

    public function show(Request $request)
    {
        // Autoriser l'affichage des articles
        $this->authorize('view', Article::class);

        try {
            // Utiliser le service pour récupérer tous les articles
            $articles = $this->articleService->all();
            
            // Retourner une réponse avec les données des articles en utilisant ArticleResource
            return $this->sendResponse(ArticleResource::collection($articles), StateEnums::SUCCESS);
        } catch (\Exception $e) {
            // Retourner une réponse avec l'erreur
            return $this->sendResponse(['error' => $e->getMessage()], StateEnums::ECHEC, 500);
        }

    }
    public function index(Request $request)
    {
        // Autoriser l'affichage des articles
        $this->authorize('view', Article::class);

        try {
            // Récupérer le paramètre 'disponible' depuis la requête
            $filters = $request->only(['disponible']);
    
            // Utiliser le service pour obtenir les articles filtrés
            $articles = $this->articleService->findByEtat($filters);
    
            // Retourner une réponse avec les données des articles en utilisant ArticleResource
            return $this->sendResponse(ArticleResource::collection($articles), StateEnums::SUCCESS);
        } catch (\Exception $e) {
            // Retourner une réponse avec l'erreur
            return $this->sendResponse(['error' => $e->getMessage()], StateEnums::ECHEC, 500);
        }
    }

    public function viewArticleById($id)
    {
        // Autoriser la visualisation d'un article
        // $this->authorize('view', Article::class);

        // Utiliser le service pour trouver l'article
        $article = $this->articleService->find($id);

        if (!$article) {
            return response()->json(['message' => 'Article non trouvé'], 404);
        }

        return response()->json($article);
    }

    ///Méthode pour voir les détails d'un article par libellé (POST)
    public function viewArticleByLibelle(Request $request)
    {
        // Autoriser la visualisation d'un article
        $this->authorize('view', Article::class);

        try {
            $libelle = $request->input('libelle');

            $article = $this->articleService->findByLibelle($libelle);

            if (!$article) {
                return response()->json(['message' => 'Article non trouvé'], 404);
            }

            return response()->json($article);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function updateArticleById(Request $request, $id)
    {
        // Validation de la quantité en stock
        $request->validate([
            'qteStock' => 'required|integer|min:0',
        ]);

        try {
            // Appeler le service pour mettre à jour l'article
            $article = $this->articleService->update($id, $request->only(['qteStock']));
            
            // Retourner la réponse JSON
            return response()->json($article);
        } catch (\Exception $e) {
            // Retourner une réponse avec l'erreur
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }

    public function updateArticleByStock(Request $request)
    {
        // Autoriser la mise à jour des articles par stock
        $this->authorize('update', Article::class);

        try {
            // Vérifier si le tableau contient au moins un article
            if (!$request->isJson() || empty($request->all())) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Le tableau doit contenir au moins un article',
                ], 400);
            }

            // Validation de la requête
            $validatedData = $request->validate([
                '*.id' => 'required|exists:articles,id',
                '*.qteStock' => 'required|integer|min:0',
            ]);

            // Utiliser le service pour mettre à jour les articles
            $result = $this->articleService->updateByStock($validatedData);

            // Réponse JSON avec les résultats
            return response()->json([
                'status' => 200,
                'data' => [
                    'success' => $result['success'],
                    'error' => $result['error'],
                ],
                'message' => count($result['success']) > 0 ? 'Quantité en stock mise à jour' : 'Aucun article mis à jour',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 422,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        // Autoriser la suppression de l'article
        $article = Article::findOrFail($id);
        $this->authorize('delete', $article);

        // Appeler le service pour supprimer l'article
        $result = $this->articleService->delete($id);

        if ($result['status'] === 'success') {
            return response()->json(['message' => $result['message']], 200);
        } else {
            return response()->json(['error' => $result['message']], 404);
        }
    }
}




















