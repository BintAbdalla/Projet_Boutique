<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArticleRequests; // Assurez-vous d'importer votre FormRequest pour la validation
use App\Http\Resources\ArticleResource; // Assurez-vous d'importer votre ArticleResource
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\Responsetrait;
use App\Enums\StateEnums;
use Exception;

class ArticleController extends Controller
{
    use Responsetrait;

    /**
     * Store a newly created article in storage.
     *
     * @param StoreArticleRequests $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreArticleRequests $request)
    {
        try {
            // Valider les données (cela se fait automatiquement avec StoreArticleRequests)
            $validatedData = $request->validated();
            
            // Utiliser une transaction pour garantir que l'opération est atomique
            DB::beginTransaction();
            
            // Créer un nouvel article
            $article = Article::create($validatedData);
            
            // Commit la transaction
            DB::commit();
            
            // Retourner une réponse avec les données de l'article créé en utilisant ArticleResource
            return $this->sendResponse(new ArticleResource($article), StateEnums::SUCCESS);
        } catch (Exception $e) {
            // En cas d'erreur, rollback la transaction
            DB::rollBack();
            
            // Retourner une réponse avec l'erreur
            return $this->sendResponse(['error' => $e->getMessage()], StateEnums::ECHEC, 500);
        }

    }

    public function show(Request $request)
    {
        try {
            // Récupérer tous les articles
            $articles = Article::all();
            
            // Retourner une réponse avec les données des articles en utilisant ArticleResource
            return $this->sendResponse(ArticleResource::collection($articles), StateEnums::SUCCESS);
        } catch (\Exception $e) {
            // Retourner une réponse avec l'erreur
            return $this->sendResponse(['error' => $e->getMessage()], StateEnums::ECHEC, 500);
        }
    }


    public function index(Request $request)
    {
        // Récupération du paramètre 'disponible' depuis l'URL
        $disponible = $request->query('disponible');
    
        // Initialisation de la requête pour obtenir les articles
        $query = Article::query();
    
        // Filtrage en fonction de la disponibilité demandée
        if ($disponible === 'oui') {
            $query->where('qteStock', '>', 0);
        } elseif ($disponible === 'non') {
            $query->where('qteStock', '=', 0);
        }
    
        // Exécution de la requête et récupération des articles
        $articles = $query->get();
    
        // Retourner les articles en réponse JSON
        return response()->json($articles);
    }
    

    public function viewArticleById($id)
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json(['message' => 'Article non trouvé'], 404);
        }

        return response()->json($article);
    }

    // Méthode pour voir les détails d'un article par libellé (POST)
    public function viewArticleByLibelle(Request $request)
    {
        $libelle = $request->input('libelle');

        $article = Article::where('libelle', $libelle)->first();

        if (!$article) {
            return response()->json(['message' => 'Article non trouvé'], 404);
        }

        return response()->json($article);
    }


    public function updateArticleById(Request $request, $id)
    {
        // Trouver l'article par ID
        $article = Article::find($id);
    
        if (!$article) {
            return response()->json(['message' => 'Article non trouvé'], 404);
        }
    
        // Validation de la quantité en stock (assurez-vous que le champ est présent et est un nombre positif)
        $request->validate([
            'qteStock' => 'required|integer|min:0',
        ]);
    
        // Mise à jour de la quantité en stock
        $article->qteStock = $request->input('qteStock');
        $article->save();
    
        return response()->json($article);
    }
    

    public function updateArticleByStock(Request $request)
    {
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
    
            // Initialisation des tableaux de résultats
            $success = [];
            $error = [];
    
            // Mise à jour des articles
            foreach ($validatedData as $articleData) {
                $article = Article::find($articleData['id']);
    
                if ($article) {
                    // Mise à jour de la quantité en stock
                    $article->qteStock = $articleData['qteStock'];
                    $article->save();
                    $success[] = $article;
                } else {
                    // Ajout à la liste des erreurs si l'article n'est pas trouvé
                    $error[] = ['id' => $articleData['id'], 'message' => 'Article non trouvé'];
                }
            }
    
            // Réponse JSON avec les résultats
            return response()->json([
                'status' => 200,
                'data' => [
                    'success' => $success,
                    'error' => $error,
                ],
                'message' => count($success) > 0 ? 'Quantité en stock mise à jour' : 'Aucun article mis à jour',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 422,
                'errors' => $e->errors(),
            ], 422);
        }
    }

    

    public function destroy($id)
    {
        try {
            $article = Article::findOrFail($id);
            $article->delete(); // Soft delete

            return response()->json(['message' => 'Article supprimé avec succès'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Article non trouvé ou autre erreur'], 404);
        }
    }
}
    




















