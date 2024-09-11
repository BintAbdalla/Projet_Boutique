<?php

namespace App\Services;

interface DetteService
{
    /**
     * Crée une nouvelle dette.
     *
     * @param array $data Les données de la dette à créer.
     * @return mixed
     */
    public function create(array $data);

    /**
     * Liste toutes les dettes de tous les clients.
     *
     * @return mixed
     */
    public function listAll();

    /**
     * Liste toutes les dettes d'un client par son ID.
     *
     * @param int $clientId L'ID du client.
     * @return mixed
     */
    public function listByClientId(int $clientId);

    /**
     * Récupère les articles associés à une dette par son ID.
     *
     * @param int $detteId L'ID de la dette.
     * @return mixed
     */
    public function getArticlesByDetteId(int $detteId);

    /**
     * Liste les dettes en fonction de leur état (solde ou non soldé).
     *
     * @param string $etat L'état des dettes ('solde' ou 'non_soldé').
     * @return mixed
     */
    public function listByEtat(string $etat);

    public function find($id);

    public function addDetteArticle(int $detteId, array $articleData);

    public function updateArticleStock(int $articleId, int $quantity);

    public function addPayment(int $detteId, float $paymentAmount);
    public function update($id, array $data);

    public function getDettes($solde = null);

    public function getDetteById($clientId);
  public function  getDetteDetailsByLibelle($detteId, $articleLibelle);

  public function getDetteByPaiement($detteId);


  public function ajouterPaiement($detteId, $montant);

  public function findDetteById($detteId);




}
