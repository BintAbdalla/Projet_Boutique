<?php

namespace App\Exceptions;

use Exception;

class ExceptionServiceDettes extends Exception
{
    /**
     * Constructeur pour les exceptions de dettes avec un message personnalisé.
     *
     * @param string $message Le message d'exception.
     * @param int $code Le code d'erreur (optionnel).
     */
    public function __construct($message = "Une erreur liée à la gestion des dettes s'est produite.", $code = 0)
    {
        parent::__construct($message, $code);
    }

    /**
     * Définir des messages d'erreur spécifiques pour les dettes.
     */
    public static function clientNotFound()
    {
        return new self("Le client n'a pas été trouvé dans la base de données.");
    }

    public static function invalidDebtAmount()
    {
        return new self("Le montant de la dette doit être positif.");
    }

    public static function articleListEmpty()
    {
        return new self("Le tableau des articles doit contenir au moins un article.");
    }

    public static function articleNotFound()
    {
        return new self("L'article spécifié n'existe pas dans la base de données.");
    }

    public static function insufficientStock()
    {
        return new self("La quantité de vente ne peut pas être supérieure à la quantité en stock.");
    }

    public static function invalidPaymentAmount()
    {
        return new self("Le montant du paiement doit être inférieur ou égal au montant de la dette.");
    }
}
