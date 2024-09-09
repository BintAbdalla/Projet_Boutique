<?php

namespace App\Services;

use App\Repository\ClientRepository;

class ClientServiceImpl implements ClientService
{
    protected $repo;

    public function __construct(ClientRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index(array $include = [])
    {
        return $this->repo->index($include);
    }

    public function store($clientData, $userData = null)
    {
        return $this->repo->store($$clientData, $userData = null);
    }

    public function filterByTelephone(string $telephone)
    {
        return $this->repo->filterByTelephone($telephone);
    }

    public function getDettes($clientId)
    {
        return $this->repo->getDettes($clientId);
    }

    public function show($id)
    {
        return $this->repo->show($id);
    }

    public function getUserForClient($id)
    {
        return $this->repo->getUserForClient($id);
    }
}
