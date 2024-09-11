<?php

namespace App\Services;

use App\Exceptions\ExceptionServiceDettes;
use App\Repository\DetteRepository;
use App\Repository\ArticleRepository;
use Illuminate\Support\Facades\Log;
use App\Models\Dettes;
use App\Models\Article;
use App\Models\Paiement;
use Illuminate\Support\Facades\DB;
use Exception;


class DetteServiceImpl implements DetteService
{
    protected $detteRepository;
    protected $articleRepository;

    public function __construct(DetteRepository $detteRepository, ArticleRepository $articleRepository)
    {
        $this->detteRepository = $detteRepository;
        $this->articleRepository = $articleRepository;
    }

    /**
     * Crée une nouvelle dette.
     *
     * @param array $data Les données de la dette à créer.
     * @return mixed
     */


    public function create(array $data): array
    {
        DB::beginTransaction(); // Démarrer une transaction pour garantir l'intégrité des données

        try {
            // Valider les articles
            $validationResult = $this->validateArticles($data['articles']);

            // Si aucun article n'est valide, annuler la transaction
            if (empty($validationResult['validatedArticles'])) {
                DB::rollBack();
                return $this->buildResponse([], $validationResult['errors']);
            }

            // Créer la dette avec les articles valides
            $dette = $this->createDette($data['clientId'], $validationResult['montantTotal']);

            // Associer les articles valides à la dette
            $this->attachArticlesToDette($dette, $validationResult['validatedArticles']);

            // Gérer le paiement
            if (isset($data['montantVerser']) && $data['montantVerser'] > 0) {
                if ($this->validatePayment($data['montantVerser'], $validationResult['montantTotal'])) {
                    $this->recordPayment($dette, $data['montantVerser']);
                } else {
                    DB::rollBack();
                    return $this->buildResponse([], ['montantVerser' => 'Le montant versé ne peut pas être supérieur au montant total des articles.']);
                }
            }

            DB::commit(); // Valider la transaction si tout est correct

            // Calculer le montant restant et utiliser les propriétés transitoires du modèle Dette
            return $this->buildResponse(
                $validationResult['validatedArticles'],
                [],
                $dette->montant,
                $dette->montantRestant, // Propriété transitoire
                $dette->montantVerser   // Propriété transitoire
            );
        } catch (Exception $e) {
            DB::rollBack(); // Annuler la transaction en cas d'erreur
            throw new Exception('Erreur lors de la création de la dette : ' . $e->getMessage());
        }
    }

    private function validateArticles(array $articles): array
    {
        $articlesCollection = collect($articles);

        // Filtrer les articles valides
        $validatedArticles = $articlesCollection->filter(function ($articleData) {
            $article = Article::find($articleData['articleId']);

            // Vérifier que l'article existe et que les informations sont valides
            return $article && $articleData['prixVente'] > 0 && $articleData['quantiteVente'] > 0 && $article->qteStock >= $articleData['quantiteVente'];
        })->map(function ($articleData) {
            $article = Article::find($articleData['articleId']);

            // Mettre à jour le stock
            $article->qteStock -= $articleData['quantiteVente'];
            $article->save();

            return [
                'articleId' => $article->id,
                'quantite' => $articleData['quantiteVente'],
                'prixVente' => $articleData['prixVente']
            ];
        })->values()->all();

        // Calculer le montant total des articles valides
        $montantTotal = collect($validatedArticles)->reduce(function ($carry, $article) {
            return $carry + ($article['quantite'] * $article['prixVente']);
        }, 0);

        // Récupérer les erreurs (articles non valides)
        $errors = $articlesCollection->reject(function ($articleData) {
            $article = Article::find($articleData['articleId']);
            return $article && $articleData['prixVente'] > 0 && $articleData['quantiteVente'] > 0 && $article->qteStock >= $articleData['quantiteVente'];
        })->map(function ($articleData) {
            $article = Article::find($articleData['articleId']);
            return $article
                ? ['articleId' => $articleData['articleId'], 'stock' => $article->qteStock]
                : ['articleId' => $articleData['articleId'], 'error' => 'Prix ou quantité invalides'];
        })->values()->all();

        return compact('validatedArticles', 'errors', 'montantTotal');
    }

    private function createDette(int $clientId, float $montantTotal): Dettes
    {
        return Dettes::create([
            'montant' => $montantTotal,
            'client_id' => $clientId,
            'date' => now(),
        ]);
    }

    private function attachArticlesToDette(Dettes $dette, array $validatedArticles): void
    {
        foreach ($validatedArticles as $article) {
            $dette->articles()->attach($article['articleId'], [
                'qteVente' => $article['quantite'],
                'prixVente' => $article['prixVente'],
            ]);
        }
    }

    private function validatePayment(float $montantVerser, float $montantTotal): bool
    {
        return $montantVerser <= $montantTotal;
    }

    private function recordPayment(Dettes $dette, float $montantVerser): void
    {
        Paiement::create([
            'dette_id' => $dette->id, // Associer le paiement à la dette
            'montant' => $montantVerser,
            'date_paiement' => now(),
        ]);
    }

    private function buildResponse(array $validatedArticles, array $errors = [], float $montantDette = 0, float $montantRestant = 0, float $montantVerser = 0): array
    {
        return [
            'validated_articles' => [
                'articles' => $validatedArticles,
                'montantDette' => $montantDette,
                'montantRestant' => $montantRestant,
                'montantVerser' => $montantVerser,
            ],
            'errors' => $errors
        ];
    }

    /**
     * Liste toutes les dettes de tous les clients.
     *
     * @return mixed
     */
    public function listAll()
    {
        $dettes = Dettes::all();
        // dd($dettes); // Affiche les données pour déboguer
        return $dettes;
    }

    /**
     * Liste toutes les dettes d'un client par son ID.
     *
     * @param int $clientId L'ID du client.
     * @return mixed
     */
    // App/Services/DetteService.php
    public function listByClientId(int $clientId)
    {
        $dettes = $this->detteRepository->listByClientId($clientId);

        // Ajouter un log pour vérifier les données retournées

        return $dettes;
    }

    /**
     * Récupère les articles associés à une dette par son ID.
     *
     * @param int $detteId L'ID de la dette.
     * @return mixed
     */
    public function getArticlesByDetteId(int $detteId)
    {
        // Vous devrez adapter cette méthode en fonction de la manière dont les articles sont stockés
        // et associés aux dettes. Ici, on suppose que le dépôt a une méthode pour cela.
        return $this->detteRepository->getArticlesByDetteId($detteId)->articles;
    }

    /**
     * Liste les dettes en fonction de leur état (solde ou non soldé).
     *
     * @param string $etat L'état des dettes ('solde' ou 'non_soldé').
     * @return mixed
     */
    public function listByEtat(string $etat)
    {
        // Assurez-vous que cette méthode retourne une collection valide
        $dettes = $this->detteRepository->listByEtat($etat);

        if (is_null($dettes)) {
            return collect(); // Retourne une collection vide si $dettes est null
        }

        return $dettes;
    }

    public function getDettes($solde = null)
    {
        // Requête de base pour récupérer les dettes avec les relations nécessaires
        $query = Dettes::query()->with('articles', 'paiements');

        // Exécuter la requête et récupérer les dettes
        $dettes = $query->get();

        if ($solde === 'soldee') {
            // Filter par montantRestant
            $dettes = $dettes->filter(function ($dette) {
                return $dette->montantRestant == 0;
            });
        } elseif ($solde === 'non_soldée') {
            // Filter par montantRestant
            $dettes = $dettes->filter(function ($dette) {
                return $dette->montantRestant > 0;
            });
        }

        return $dettes;
    }

    public function find($id) {}

    /**
     * Ajoute un article à une dette.
     *
     * @param int $detteId L'ID de la dette.
     * @param array $article Les détails de l'article.
     */
    function addDetteArticle(int $detteId, array $article): void
    {
        DB::table('article_dette')->insert([
            'dette_id' => $detteId,
            'article_id' => $article['articleId'],
            'qte_vente' => $article['qteVente'],
            'prix_vente' => $article['prixVente'],
        ]);
    }

    /**
     * Met à jour la quantité en stock d'un article.
     *
     * @param int $articleId L'ID de l'article.
     * @param int $qteVente La quantité vendue.
     */
    function updateArticleStock($articleId, $qteVente)
    {
        $article = DB::table('articles')->where('id', $articleId)->first();

        if (!$article) {
            throw new \Exception('L\'article avec l\'ID spécifié n\'existe pas.');
        }

        if ($article->qte_stock < $qteVente) {
            throw new \Exception('La quantité demandée pour l\'article avec l\'ID ' . $articleId . ' dépasse le stock disponible.');
        }

        DB::table('articles')->where('id', $articleId)->update([
            'qte_stock' => $article->qte_stock - $qteVente
        ]);
    }

    /**
     * Ajoute un paiement à une dette.
     *
     * @param int $detteId L'ID de la dette.
     * @param float $paymentAmount Le montant du paiement.
     * @return float Le montant restant après paiement.
     */
    function addPayment(int $detteId, float $paymentAmount): void
    {
        DB::table('paiements')->insert([
            'dette_id' => $detteId,
            'montant' => $paymentAmount['montant'],
            'date_paiement' => now(),
        ]);

        // Mettre à jour le montant restant
        $totalPaid = DB::table('paiements')
            ->where('dette_id', $detteId)
            ->sum('montant');

        $dette = DB::table('dettes')->where('id', $detteId)->first();

        if (!$dette) {
            throw new \Exception('La dette avec l\'ID spécifié n\'existe pas.');
        }

        $montantRestant = max(0, $dette->montant - $totalPaid);

        DB::table('dettes')->where('id', $detteId)->update([
            'montant_restant' => $montantRestant,
            'etat' => $montantRestant == 0 ? 'soldee' : 'non_soldée'
        ]);
    }

    /**
     * Met à jour une dette.
     *
     * @param int $id L'ID de la dette.
     * @param array $data Les données à mettre à jour.
     */
    function update($id, array $data)
    {
        $this->detteRepository->update($id, $data);
    }
    public function getDetteById($clientId)
    {
        // Requête pour récupérer les dettes du client avec les relations articles et paiements
        $dettes = Dettes::where('client_id', $clientId)
            ->with('articles', 'paiements') // Chargement des relations
            ->get();

        // Retourner les dettes du client
        return $dettes;
    }

    // Méthode pour récupérer les détails d'une dette spécifique avec un article par son libellé
    public function getDetteDetailsByLibelle($detteId, $articleLibelle)
    {
        // Requête pour récupérer la dette par son ID et filtrer les articles par leur libellé
        $dette = Dettes::where('id', $detteId)
            ->with(['articles' => function ($query) use ($articleLibelle) {
                // Filtrer par le libellé de l'article
                $query->where('libelle', $articleLibelle);
            }, 'paiements'])
            ->first();

        // Retourner la dette avec ses articles filtrés par libellé et paiements
        return $dette;
    }

    public function getDetteByPaiement($detteId)
    {
        // Rechercher la dette avec ses paiements
        $dette = Dettes::with('paiements')->find($detteId);

        // Vérifier si la dette existe
        if (!$dette) {
            return null;
        }

        return $dette;
    }

    public function findDetteById($detteId) {}
    public function ajouterPaiement($detteId, $montant)
    {
        // Trouver la dette
        $dette = Dettes::find($detteId);

        if (!$dette) {
            // Retourner une erreur si la dette n'est pas trouvée
            return [
                'status' => 'error',
                'message' => 'Dette non trouvée',
                'data' => null
            ];
        }

        // Calculer le montant restant à partir des paiements existants
        $montantRestant = $dette->montant - $dette->paiements()->sum('montant');

        // Vérifier si le montant est valide
        if ($montant <= 0 || $montant > $montantRestant) {
            return [
                'status' => 'error',
                'message' => 'Le montant est invalide',
                'data' => null
            ];
        }

        // Ajouter le paiement
        Paiement::create([
            'dette_id' => $detteId,
            'montant' => $montant,
            'date_paiement' => now(),
        ]);

        // Mettre à jour le montant de la dette
        $nouveauMontant = $dette->montant - $montant;
        $dette->montant = $nouveauMontant;
        $dette->save();

        // Rafraîchir les données de la dette
        $dette->refresh();

        return [
            'status' => 'success',
            'message' => 'Paiement effectué avec succès',
            'data' => [
                'dette' => $dette,
                'paiements' => $dette->paiements
            ]
        ];
    }
}
