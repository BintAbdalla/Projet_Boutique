<?php
namespace App\Repository;

use App\Models\Article;

class EloquentArticleRepository implements ArticleRepository
{
    public function all()
    {
        return Article::all();
    }

    public function create(array $data)
    {
        return Article::create($data);
    }

    public function find($id)
    {
        return Article::find($id);
    }

    public function update($id, array $data)
    {
        $article = $this->find($id);
        $article->update($data);
        return $article;
    }

    public function delete($id)
    {
        $article = $this->find($id);
        return $article->delete();
    }

    public function findByLibelle($libelle)
    {
        return Article::where('libelle', $libelle)->first();
    }

    public function findByEtat($etat)
    {
        return Article::where('etat', $etat)->get();
    }

    public function updateByStock($articlesData){

        return Article::where('stock', $articlesData)->update($articlesData);
    }
}
