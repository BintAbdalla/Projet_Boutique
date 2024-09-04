<?php

namespace App\Services;

use App\Repository\ArticleRepository;
use App\Models\Article;
use App\Http\Resources\ArticleResource;
use Illuminate\Support\Facades\DB;
use Exception;

class ArticleServiceImpl implements ArticleService
{
    protected $repo;

    public function __construct(ArticleRepository $repo)
    {
        $this->repo = $repo;
    }

    public function all()
    {
        // Récupérer tous les articles en utilisant le dépôt
        return $this->repo->all();
    }

    public function create(array $data)
    {
        try {
            // Utiliser une transaction pour garantir que l'opération est atomique
            DB::beginTransaction();
            
            // Créer un nouvel article avec les données validées
            $article = Article::create($data);
            
            // Commit la transaction
            DB::commit();
            
            // Retourner l'article créé
            return new ArticleResource($article);
        } catch (Exception $e) {
            // En cas d'erreur, rollback la transaction
            DB::rollBack();
            
            // Lever une exception pour être gérée par le contrôleur
            throw new Exception($e->getMessage());
        }
    }

    public function find($id)
    {
        // Rechercher l'article par ID en utilisant le dépôt
        return $this->repo->find($id);
    }
    public function update($id, array $data)
    {
        // Trouver l'article par ID
        $article = Article::find($id);

        if (!$article) {
            throw new \Exception('Article non trouvé');
        }

        // Mise à jour des attributs
        $article->fill($data);
        $article->save();

        return $article;
    }

    public function delete($id)
    {
        // Trouver l'article par ID
        $article = Article::findOrFail($id);

        // Essayer de supprimer l'article (soft delete)
        try {
            $article->delete(); // Soft delete
            return ['status' => 'success', 'message' => 'Article supprimé avec succès'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Erreur lors de la suppression de l\'article'];
        }
    }

    public function findByLibelle($libelle)
    {
        // Le scope global 'filter' est appliqué automatiquement
        return Article::where('libelle', $libelle)->first();
    }

    public function findByEtat($etat)
    {
         // Appliquer les filtres en fonction des paramètres fournis
        $query = Article::query();

    }
         public function updateByStock(array $articlesData)
         {
            $success = [];
            $error = [];
    
            foreach ($articlesData as $articleData) {
                $article = Article::find($articleData['id']);
    
                if ($article) {
                    // Ajouter la nouvelle quantité à la quantité existante en stock
                    $article->qteStock += $articleData['qteStock'];
                    $article->save();
                    $success[] = $article;
                } else {
                    // Ajout à la liste des erreurs si l'article n'est pas trouvé
                    $error[] = ['id' => $articleData['id'], 'message' => 'Article non trouvé'];
                }
            }
    
            return [
                'success' => $success,
                'error' => $error
            ];
        }
    
}
