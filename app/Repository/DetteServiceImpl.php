<?php

namespace App\Repository;

use App\Repository\DetteRepository;
use App\Models\Dettes;
use Illuminate\Support\Facades\DB;

class DetteServiceImpl implements DetteRepository{

    protected $dette;

    /**
     * 
     * Crée une nouvelle dette.
     *
     * @param array $data Les données de la dette à créer.
     * @return mixed
     */
    public function create(array $data){
    return Dettes::create($data);

    }

    

    /**
     * Liste toutes les dettes de tous les clients.
     *
     * @return mixed
     */
    public function listAll(){
        return $this->dette;
    }

    /**
     * Liste toutes les dettes d'un client par son ID.
     *
     * @param int $clientId L'ID du client.
     * @return mixed
     */
    public function listByClientId(int $clientId){
        return $this->dette=$clientId;
    }

    /**
     * Récupère les articles associés à une dette par son ID.
     *
     * @param int $detteId L'ID de la dette.
     * @return mixed
     */
    
    /**
     * Liste les dettes en fonction de leur état (solde ou non soldé).
     *
     * @param string $etat L'état des dettes ('solde' ou 'non_soldé').
     * @return mixed
     */
    public function listByEtat(string $etat){
        // protected $etat;
        return $this->dette=$etat;
    }
    
    public function find($id){
        return $this->dette=$id;
    }

    public function addDetteArticle(int $detteId, array $articleData){
        
    }
    
    public function updateArticleStock(int $articleId, int $quantity){
        // protected $articleId;
        // protected $quantity;
        return $this->dette=$articleId;
        // return $this->quantity=$quantity;
    }
    
    public function addPayment(int $detteId, float $paymentAmount){
        // protected $paymentAmount;
        return $this->dette=$detteId;
        // return $this->paymentAmount=$paymentAmount;
    }
    public function update($id, array $data){
        
    }
        public function getArticlesByDetteId(int $detteId){
            return $this->dette=$detteId;
        }
}