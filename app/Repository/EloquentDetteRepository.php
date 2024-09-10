<?php

namespace App\Repository;

use App\Models\Dettes;
use App\Models\Article;
use App\Models\Paiement;

class EloquentDetteRepository implements DetteRepository
{
    /**
     * Crée une nouvelle dette.
     *
     * @param array $data Les données de la dette à créer.
     * @return mixed
     */
    public function create(array $data)
    {
        return Dettes::create($data);
    }

    /**
     * Liste toutes les dettes de tous les clients.
     *
     * @return mixed
     */
    public function listAll()
    {
        return Dettes::all();
    }



    public function update($id, array $data)
    {
        // Trouver la dette par son ID
        $dette = Dettes::find($id);

        // Si la dette n'existe pas, lancer une exception
        if (!$dette) {
            // throw new ModelNotFoundException("Dette avec ID {$id} non trouvée.");
        }

        // Mettre à jour les données de la dette
        $dette->update($data);

        return $dette;
    }
    /**
     * Liste toutes les dettes d'un client par son ID.
     *
     * @param int $clientId L'ID du client.
     * @return mixed
     */
    public function listByClientId(int $clientId)
    {
        return Dettes::where('client_id', $clientId)->get();
    }

    /**
     * Récupère les articles associés à une dette par son ID.
     *
     * @param int $detteId L'ID de la dette.
     * @return mixed
     */
    public function getArticlesByDetteId(int $detteId)
    {
        return Dettes::find($detteId)->articles;
    }

    /**
     * Liste les dettes en fonction de leur état (solde ou non soldé).
     *
     * @param string $etat L'état des dettes ('solde' ou 'non_soldé').
     * @return mixed
     */
    public function listByEtat(string $etat)
    {
        if ($etat === 'solde') {
            return Dettes::where('etat', 'solde')->get();
        } else {
            return Dettes::where('etat', 'non_soldé')->get();
        }
    }

    /**
     * Récupère une dette par son ID.
     *
     * @param int $id L'ID de la dette.
     * @return mixed
     */
    public function find($id)
    {
        return Dettes::find($id);
    }

    /**
     * Ajoute un article à une dette.
     *
     * @param int $detteId L'ID de la dette.
     * @param array $articleData Les données de l'article.
     * @return void
     */
    public function addDetteArticle(int $detteId, array $articleData)
    {
        $dette = Dettes::find($detteId);
        if ($dette) {
            $dette->articles()->create($articleData);
        }
    }

    /**
     * Met à jour la quantité en stock d'un article.
     *
     * @param int $articleId L'ID de l'article.
     * @param int $quantity La quantité à mettre à jour.
     * @return void
     */
    public function updateArticleStock(int $articleId, int $quantity)
    {
        $article = Article::find($articleId);
        if ($article) {
            $article->quantity -= $quantity;
            $article->save();
        }
    }

    /**
     * Enregistre un paiement pour une dette.
     *
     * @param int $detteId L'ID de la dette.
     * @param float $paymentAmount Le montant du paiement.
     * @return void
     */
    public function addPayment(int $detteId, float $paymentAmount)
    {
        // Ajoutez un paiement pour la dette
        $payment = Paiement::find()([
            'dette_id' => $detteId,
            'montant' => $paymentAmount
        ]);
        $payment->save();

        // Mettez à jour le montant total payé de la dette
        $dette = Dettes::find($detteId);
        if ($dette) {
            $dette->paiement += $paymentAmount;
            $dette->save();
        }
    }
}
