<?php


namespace App\Repository;

interface ClientRepository
{
    public function index(array $include = []);

    public function store($clientData, $userData = null);

    public function filterByTelephone(string $telephone);

    public function getDettes($clientId);

    public function show($id);

    public function getUserForClient($id);
}
