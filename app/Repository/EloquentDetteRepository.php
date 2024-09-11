<?php

namespace App\Repository;

use App\Models\Dettes;
use App\Models\Article;
use App\Models\Paiement;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EloquentDetteRepository implements DetteRepository
{
    /**
     * Crée une nouvelle dette.
     *
     * @param int $clientId L'ID du client.
     * @param float $montantTotal Le montant total de la dette.
     * @return Dettes
     */
    public function createDette(int $clientId, float $montantTotal): Dettes
    {
        return Dettes::create([
            'client_id' => $clientId,
            'montant_total' => $montantTotal,
            'montant_paye' => 0,
            'etat' => 'non_soldé',
        ]);
    }

    /**
     * Ajoute des articles à une dette.
     *
     * @param int $detteId L'ID de la dette.
     * @param array $validatedArticles Les articles validés à ajouter.
     * @return void
     * @throws ModelNotFoundException
     */
    public function attachArticlesToDette(int $detteId, array $validatedArticles): void
    {
        $dette = Dettes::find($detteId);

        if (!$dette) {
            throw new ModelNotFoundException("Dette avec ID {$detteId} non trouvée.");
        }

        foreach ($validatedArticles as $articleData) {
            $article = Article::find($articleData['article_id']);

            if (!$article) {
                throw new ModelNotFoundException("Article avec ID {$articleData['article_id']} non trouvé.");
            }

            $dette->articles()->attach($articleData['article_id'], [
                'quantite' => $articleData['quantite'],
                'prix_vente' => $articleData['prix_vente'],
            ]);

            // Mettre à jour le stock de l'article
            $this->updateArticleStock($articleData['article_id'], $articleData['quantite']);
        }
    }

    /**
     * Enregistre un paiement pour une dette.
     *
     * @param int $detteId L'ID de la dette.
     * @param float $montantVerser Le montant du paiement.
     * @return void
     * @throws ModelNotFoundException
     */
    public function recordPayment(int $detteId, float $montantVerser): void
    {
        $dette = Dettes::find($detteId);

        if (!$dette) {
            throw new ModelNotFoundException("Dette avec ID {$detteId} non trouvée.");
        }

        Paiement::create([
            'dette_id' => $detteId,
            'montant' => $montantVerser,
            'date_paiement' => now(),
        ]);

        // Mettre à jour le montant total payé de la dette
        $dette->montant_paye += $montantVerser;
        $dette->save();
    }

    // Autres méthodes existantes

    public function create(array $data)
    {
        // Implémentation existante
    }

    public function listAll()
    {
        // Implémentation existante
    }

    public function update( $id,$data)
    {
        // Implémentation existante
    }

    public function listByClientId(int $clientId)
    {
        // Implémentation existante
    }

    public function getArticlesByDetteId(int $detteId)
    {
        // Implémentation existante
    }

    public function listByEtat(string $etat)
    {
        // Implémentation existante
    }

    public function find($id)
    {
        // Implémentation existante
    }

    public function addDetteArticle(int $detteId, array $articleData)
    {
        // Implémentation existante
    }

    public function updateArticleStock(int $articleId, int $quantity)
    {
        // Implémentation existante
    }

    public function addPayment(int $detteId, float $paymentAmount)
    {
        // Implémentation existante
    }

    public function getDettes($solde = null):void{

    }
    public function getDetteById($clientId):void{

    }

    public function  getDetteDetailsByLibelle($detteId, $articleLibelle):void{

    }

    public function getDetteByPaiement($detteId){

    }

    public function ajouterPaiement($detteId, $montant){

    }


    public function findDetteById($detteId){
        
    }
}
