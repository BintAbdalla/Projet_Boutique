<?php

namespace App\Repository;

use App\Models\Dettes;
use App\Models\Paiement;
use Illuminate\Support\Facades\DB;

interface DetteRepository
{
    public function createDette(int $clientId, float $montantTotal): Dettes;

    public function attachArticlesToDette(int $detteId, array $validatedArticles): void;

    public function recordPayment(int $detteId, float $montantVerser): void;

    public function find($id);

    public function listAll();

    public function listByClientId(int $clientId);

    public function listByEtat(string $etat);

    public function update($id, array $data);

    public function getArticlesByDetteId(int $detteId);

    public function addDetteArticle(int $detteId, array $articleData);

    public function updateArticleStock(int $articleId, int $quantity);

    public function addPayment(int $detteId, float $paymentAmount);

    public function getDettes($solde = null);

    public function getDetteById($clientId);

    public function  getDetteDetailsByLibelle($detteId, $articleLibelle);

    public function getDetteByPaiement($detteId);

    public function ajouterPaiement($detteId, $montant);

    public function findDetteById($detteId);
}
