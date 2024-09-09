<?php

namespace App\Services;

use App\Repository\ArticleRepository;

class ArticleServiceImpl implements ArticleService
{
    protected $repo;

    public function __construct(ArticleRepository $repo)
    {
        $this->repo = $repo;
    }

    public function all()
    {
        return $this->repo->all();
    }

    public function create(array $data)
    {
        return $this->repo->create($data);
    }

    public function find($id)
    {
        return $this->repo->find($id);
    }

    public function update($id, array $data)
    {
        return $this->repo->update($id, $data);
    }

    public function delete($id)
    {
        return $this->repo->delete($id);
    }

    public function findByLibelle($libelle)
    {
        return $this->repo->findByLibelle($libelle);
    }

    public function findByEtat($etat)
    {
        return $this->repo->findByEtat($etat);
    }

    public function updateByStock($stock){
        return $this->repo->updateByStock($stock);
    }
}
