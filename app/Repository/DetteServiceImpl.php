<?php

namespace App\Repository;

use App\Models\Dettes;
use App\Models\Paiement;
use Illuminate\Support\Facades\DB;
use App\Repository\ArticleRepository;
use App\Models\Article;
use Illuminate\Support\Facades\Log;

class DetteServiceImpl implements DetteRepository
{
    public function createDette(int $clientId, float $montantTotal): Dettes
    {
        return Dettes::create([
            'montant' => $montantTotal,
            'client_id' => $clientId,
            'date' => now()->format('Y-m-d'),
        ]);
    }

    public function attachArticlesToDette(int $detteId, array $validatedArticles): void
    {
        $dette = Dettes::find($detteId);
        foreach ($validatedArticles as $article) {
            $dette->articles()->attach($article['articleId'], [
                'qteVente' => $article['quantite'],
                'prixVente' => $article['prixVente'],
            ]);
        }
    }

    public function recordPayment(int $detteId, float $montantVerser): void
    {
        Paiement::create([
            'dette_id' => $detteId,
            'montant' => $montantVerser,
            'date_paiement' => now(),
        ]);
    }

    public function find($id)
    {
        return Dettes::find($id);
    }

    public function listAll()
    {
        // Récupérer toutes les dettes
        return Dettes::with(['clients', 'articles', 'paiements'])->get();

        // dd($this->Dettes->listAll());
    }
    // App/Repositories/DetteRepository.php
    // App/Repositories/DetteRepository.php
    public function listByClientId(int $clientId)
    {
        $dettes = Dettes::where('client_id', $clientId)->get();
        // dd($dettes); // Vérifiez le résultat ici
        return $dettes;
    }


public function listByEtat(string $etat)
{
    $dettes = Dettes::with('paiements')->get();

    if ($etat === 'soldee') {
        return $dettes->filter(function ($dette) {
            return $dette->montantRestant == 0;
        });
    } elseif ($etat === 'non_soldée') {
        return $dettes->filter(function ($dette) {
            return $dette->montantRestant != 0;
        });
    } else {
        return collect(); // Retourne une collection vide
    }
}

public function getDettes($solde = null)
{
    // Requête de base pour récupérer les dettes avec les relations nécessaires
    $query = Dettes::query()->with('articles','paiements');

    // Exécuter la requête et récupérer les dettes
    return $query->get()->map(function ($dette) {
        // Ajoute les calculs dynamiques au modèle
        $dette['montantVerser']= $dette->montantVerser;
        $dette['montantRestant'] = $dette->montantRestant;

        return $dette;
    });
}
    
    


    public function update($id, array $data)
    {
        $dette = Dettes::find($id);
        if ($dette) {
            $dette->update($data);
        }
        return $dette;
    }

    public function getArticlesByDetteId(int $detteId)
    {
        $dette = Dettes::find($detteId);
        return $dette ? $dette->articles : null;
    }

    public function addDetteArticle(int $detteId, array $articleData)
    {
        // Exemple d'ajout d'article à une dette
    }

    public function updateArticleStock(int $articleId, int $quantity)
    {
        $article = Article::find($articleId);
        if ($article) {
            $article->qteStock -= $quantity;
            $article->save();
        }
    }

    public function addPayment(int $detteId, float $paymentAmount)
    {
        // Exemple d'ajout d'un paiement
    }

    public function getDetteById($clientId): string{
        $client = Dettes::find($clientId);
        return $client? $client->montant : 'Dette non trouvée';
    }

    public function  getDetteDetailsByLibelle($detteId, $articleLibelle){
        $article = Article::where('libelle', $articleLibelle)->first();
        if($article){
            $dette = Dettes::find($detteId);
            $details = $dette->details->where('id_article', $article->id)->first();
            return $details? $details->pivot->qteVente : 'Article non trouvé';
        }
        return 'Article non trouvé';  // Si l'article n'est pas trouvé, renvoie un message d'erreur
    }


    public function getDetteByPaiement($detteId){
        // $dette = Dettes::find($detteId);
        // if($dette){
        //     $paiement = Paiement::where('montant', $paiement)->first();
        //     return $paiement? $paiement->date_paiement : 'Paiement non trouvé';
        // }
        // return 'Paiement non trouvé';  // Si le paiement n'est pas trouvé, renvoie un message d'erreur
    }
    public function findDetteById($detteId)
    {
        return Dettes::find($detteId);
    }

    public function ajouterPaiement($detteId, $montant)
    {
        $dette = $this->findDetteById($detteId);
        dd($dette);

        if (!$dette) {
            return null; // ou gérer l'erreur comme vous le souhaitez
        }

        // Mettre à jour le montant versé
        $dette->montantVerser += $montant;
        $dette->save();

        // Ajouter le paiement
        Paiement::create([
            'dette_id' => $detteId,
            'montant' => $montant,
            'date_paiement' => now(),
        ]);

        // Mettre à jour les montants
        $dette->refresh();

        return $dette;
    }


}
