<?php

namespace App\Observers;

use App\Models\Dettes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DetteObserver
{
    /**
     * Handle the Dette "creating" event.
     *
     * @param Dettes $dette
     * @return void
     */
    // public function creating(Dettes $dette): void
    // {
    //     Log::info('Avant la création de la dette : ', ['dette_id' => $dette->id]);

    //     // Vérifier le montant de la dette
    //     if ($dette->montant <= 0) {
    //         throw new \Exception('Le montant de la dette doit être supérieur à 0.');
    //     }

    //     // Vérifier que les articles sont fournis
    //     if (empty($dette->articles)) {
    //         throw new \Exception('La liste des articles pour la dette ne peut pas être vide.');
    //     }

    //     // Vérifier l'existence du client
    //     $clientExists = DB::table('clients')->where('id', $dette->client_id)->exists();
    //     if (!$clientExists) {
    //         throw new \Exception('Le client spécifié n\'existe pas.');
    //     }

    //     // Vérifier les quantités des articles en stock
    //     foreach ($dette->articles as $article) {
    //         $articleInStock = DB::table('articles')->where('id', $article['article_id'])->first();

    //         if (!$articleInStock) {
    //             throw new \Exception('L\'article avec l\'ID ' . $article['article_id'] . ' n\'existe pas.');
    //         }

    //         if ($articleInStock->qte_stock < $article['qte_vente']) {
    //             throw new \Exception('La quantité demandée pour l\'article avec l\'ID ' . $article['article_id'] . ' dépasse le stock disponible.');
    //         }
    //     }
    // }

    /**
     * Handle the Dette "created" event.
     *
     * @param Dettes $dette
     * @return void
     */
    // public function created(Dettes $dette): void

    // {
    //     Log::info('Après la création de la dette : ', ['dette_id' => $dette->id]);

    //     DB::beginTransaction();

    //     try {
    //         // 1. Ajouter les détails de la dette dans la table `article_dette`
    //         foreach ($dette->articles as $article) {
    //             DB::table('article_dette')->insert([
    //                 'dette_id' => $dette->id,
    //                 'article_id' => $article['article_id'],
    //                 'qteVente' => $article['qteVente'],
    //                 'prixVente' => $article['prixVente'],
    //             ]);
    //         }

    //         // 2. Mettre à jour la quantité en stock dans la table `articles`
    //         foreach ($dette->articles as $article) {
    //             $articleInStock = DB::table('articles')->where('id', $article['article_id'])->first();

    //             if (!$articleInStock) {
    //                 throw new \Exception('L\'article avec l\'ID spécifié n\'existe pas.');
    //             }

    //             if ($articleInStock->qte_stock < $article['qteVente']) {
    //                 throw new \Exception('La quantité demandée pour l\'article avec l\'ID ' . $article['article_id'] . ' dépasse le stock disponible.');
    //             }

    //             DB::table('articles')->where('id', $article['article_id'])->update([
    //                 'qteStock' => $articleInStock->qte_stock - $article['qteVente']
    //             ]);
    //         }

    //         // 3. Enregistrer le paiement dans la table `paiements` si un paiement est fourni
    //         if (isset($dette->paiement)) {
    //             DB::table('paiements')->insert([
    //                 'dette_id' => $dette->id,
    //                 'montant' => $dette->paiement,
    //                 'date_paiement' => now(),
    //             ]);

    //             // Calculer le montant restant
    //             $totalPaid = DB::table('paiements')
    //                 ->where('dette_id', $dette->id)
    //                 ->sum('montant');

    //             $montantRestant = max(0, $dette->montant - $totalPaid);

    //             // Mettre à jour la dette avec le montant restant
    //             DB::table('dettes')->where('id', $dette->id)->update([
    //                 'montant_restant' => $montantRestant,
    //                 'etat' => $montantRestant == 0 ? 'soldee' : 'non_soldée'
    //             ]);
    //         } else {
    //             // Si aucun paiement, mettre à jour l'état de la dette en non soldée
    //             DB::table('dettes')->where('id', $dette->id)->update([
    //                 'montant_restant' => $dette->montant,
    //                 'etat' => 'non_soldée'
    //             ]);
    //         }

    //         DB::commit();
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('Erreur après la création de la dette : ' . $e->getMessage());
    //         throw $e; // Propager l'exception pour qu'elle soit gérée ailleurs
    //     }
    // }
  
  
    public function created(Dettes $dette): void
    {
        Log::info('Observer appelé après création de la dette', ['dette_id' => $dette->id]);
        // Votre logique ici...
    }
    
  
}
