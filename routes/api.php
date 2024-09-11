<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\DetteController;


// Route pour se connecter
Route::post('/login', [AuthController::class, 'login'])->name('login');
// Route::get('/login', [AuthController::class, 'login'])->name('login');

// Route protégée
// Route::middleware('auth:api')->post('/register', function (Request $request) {
//     return $request->register();
// });



use App\Models\User;

// Route pour obtenir tous les utilisateurs
Route::get('v1/users', function () {
    return response()->json(User::all());
});

// Route pour obtenir un utilisateur par ID
Route::get('v1/users/{id}', function ($id) {
    return response()->json(User::findOrFail($id));
});





// Groupe de routes pour les utilisateurs avec préfixe 'api/v1/users' et alias
Route::prefix('v1/users')->middleware('auth:api')->group(function () {
    Route::post('/', [UserController::class, 'create'])->name('users.create');
    Route::get('/{id}', [UserController::class, 'show'])->name('users.show');
    Route::put('/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/{Id}/users', [UserController::class, 'createUserForClient'])->name('Users.create');
    Route::get('/', [UserController::class, 'filterUsersByRole'])->name('Users.filterUsersByRole');
    Route::get('/', [UserController::class, 'filterUsersByRoleAndState'])->name('Users.filterUsersByRoleAndState');
    Route::get('/', [UserController::class, 'filterUsersByRoleAndState'])->name('Users.filterUsersByRoleAndState');
    Route::patch('/{id}', [UserController::class, 'update'])->name('users.updates');
    Route::delete('/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    // Route pour créer un utilisateur associé à un client existant
});




Route::prefix('v1/clients')->middleware('auth:api')->group(function () {
// Route pour créer un nouveau client
Route::post('/', [ClientController::class, 'store'])->name('clients.store');
    // Route pour obtenir tous les clients
    Route::get('/', [ClientController::class, 'index'])->name('clients.index');
    
    Route::post('/telephone', [ClientController::class, 'filterByTelephone']);
    // Route pour obtenir un client spécifique par ID
    Route::get('/{id}', [ClientController::class, 'show'])->name('clients.show');
    
    Route::post('/{id}/user', [ClientController::class, 'getUserForClient'])->name('clients.user');
    
    Route::post('/{id}/dettes', [ClientController::class, 'getDettes'])->name('clients.dettes');
    // Route pour mettre à jour un client spécifique par ID (PUT ou PATCH)
    Route::match(['put', 'patch'], '/{id}', [ClientController::class, 'update'])->name('clients.update');
    
    // Route pour supprimer un client spécifique par ID
    Route::delete('/{id}', [ClientController::class, 'destroy'])->name('clients.destroy');
    // Route pour créer un utilisateur associé à un client existant
    // Route::post('/{Id}/users', [UserController::class, 'createUserForClient']);
    
});

Route::prefix('v1/articles')->middleware('auth:api')->group(function () {
    // Route pour créer un nouvel article
    Route::post('/', [ArticleController::class, 'store'])->name('articles.store');

    // Route pour obtenir tous les articles
    Route::get('/', [ArticleController::class, 'show'])->name('articles.show');

    // Route pour obtenir les articles disponibles en fonction d'une disponibilité (oui/non)
    Route::get('/disponibles', [ArticleController::class, 'index'])->name('articles.disponibles');

    // Route pour obtenir un article spécifique par ID
    Route::get('/{id}', [ArticleController::class, 'viewArticleById'])->name('articles.viewById');

    // Route pour obtenir un article spécifique par libellé (POST)
    Route::post('/libelle', [ArticleController::class, 'viewArticleByLibelle'])->name('articles.viewByLibelle');

    // Route pour mettre à jour un article spécifique par ID
    Route::patch('/{id}', [ArticleController::class, 'updateArticleById'])->name('articles.updateById');

    // Route pour mettre à jour un article spécifique par stock (POST)
    Route::post('/stock', [ArticleController::class, 'updateArticleByStock'])->name('articles.updateByStock');

    // // Route pour supprimer un article spécifique par ID
    Route::delete('/{id}', [ArticleController::class, 'destroy'])->name('articles.destroy');

});


Route::prefix('v1/dettes')->middleware('auth:api')->group(function () {
    // Route pour créer une nouvelle dette
    Route::post('/', [DetteController::class,'create'])->name('dettes.create');

    // Route pour obtenir tous les dettes
    Route::get('/', [DetteController::class, 'listAll'])->name('dettes.listAll');

    // Route pour obtenir une dette spécifique par ID
    Route::get('/{id}', [DetteController::class,'listByClientId'])->name('dettes.listByClientId');

    // Route pour obtenir une dette solde ou non Soldee
    Route::get('/', [DetteController::class,'listByStatus'])->name('dettes.listByStatus');

    // Route pour obtenir une dette par date de création
    Route::get('/', [DetteController::class,'getEtat'])->name('dettes.getEtat');
    
    Route::get('/{id}', [DetteController::class, 'getDetteById'])->name('dettes.getDetteById');

    Route::get('/{id}/article/{libelle}', [DetteController::class, 'getDetteDetailsByLibelle'])->name('dettes.getDetteDetailsByLibelle');

   

Route::post('/{id}/paiements', [DetteController::class, 'listPaiementsByDette'])->name('dettes.listPaiementsByDette');
     
    // Route pour payer une dette spécifique par ID

    Route::post('/{id}/paiements', [DetteController::class, 'ajouterPaiement'])->name('dettes.ajouterPaiement');


    // Route pour mettre à jour une dette spécifique par ID
    Route::patch('/{id}', [DetteController::class, 'update'])->name('dettes.update');

    // Route pour supprimer une dette spécifique par ID
    Route::delete('/{id}', [DetteController::class, 'destroy'])->name('dettes.destroy');
});