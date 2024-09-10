<?php

namespace App\Exceptions;

use Exception;

class ExceptionRepositoryDettes extends Exception
{
    /**
     * Constructeur pour les exceptions de repository liées aux dettes.
     *
     * @param string $message Le message d'exception.
     * @param int $code Le code d'erreur (optionnel).
     */
    public function __construct($message = "Une erreur liée au repository des dettes s'est produite.", $code = 0)
    {
        parent::__construct($message, $code);
    }

    /**
     * Exception lorsqu'une dette ne peut pas être trouvée dans le repository.
     */
    public static function debtNotFound()
    {
        return new self("La dette demandée n'a pas été trouvée.");
    }

    /**
     * Exception lorsqu'une erreur se produit lors de la création d'une dette.
     */
    public static function debtCreationFailed()
    {
        return new self("Une erreur s'est produite lors de la création de la dette.");
    }

    /**
     * Exception lorsqu'une erreur se produit lors de la mise à jour d'une dette.
     */
    public static function debtUpdateFailed()
    {
        return new self("Une erreur s'est produite lors de la mise à jour de la dette.");
    }

    /**
     * Exception lorsque la suppression d'une dette échoue.
     */
    public static function debtDeletionFailed()
    {
        return new self("Une erreur s'est produite lors de la suppression de la dette.");
    }

    /**
     * Exception lorsqu'une connexion à la base de données échoue.
     */
    public static function databaseConnectionFailed()
    {
        return new self("La connexion à la base de données a échoué.");
    }

    /**
     * Exception lorsqu'une opération de requête échoue.
     */
    public static function queryFailed()
    {
        return new self("Une erreur s'est produite lors de l'exécution de la requête.");
    }
}
