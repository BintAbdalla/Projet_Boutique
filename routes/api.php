<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ArticleController;

// Route pour se connecter
Route::post('/login', [AuthController::class, 'login']);

// Route protégée
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();

});



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
Route::prefix('v1/users')->group(function () {
    Route::post('/', [UserController::class, 'create'])->name('users.create'); 
    Route::put('/{id}', [UserController::class, 'update'])->name('users.update'); 
    Route::patch('/{id}', [UserController::class, 'update'])->name('users.update'); 
    Route::delete('/{id}', [UserController::class, 'destroy'])->name('users.destroy'); 
});



Route::prefix('v1/clients')->middleware('auth:api')->group(function () {
    // Route pour obtenir tous les clients
    Route::get('/', [ClientController::class, 'index'])->name('clients.index');

    // Route pour obtenir un client spécifique par ID
    Route::get('/{id}', [ClientController::class, 'show'])->name('clients.show');

    // Route pour créer un nouveau client
    Route::post('/', [ClientController::class, 'store'])->name('clients.store');

    // Route pour mettre à jour un client spécifique par ID (PUT ou PATCH)
    Route::match(['put', 'patch'], '/{id}', [ClientController::class, 'update'])->name('clients.update');

    // Route pour supprimer un client spécifique par ID
    Route::delete('/{id}', [ClientController::class, 'destroy'])->name('clients.destroy');
});

Route::prefix('v1/articles')->middleware('auth:api')->group(function (){
    // Route pour créer un nouvel article
    Route::post('/', [ArticleController::class,'store'])->name('articles.store');

    // Route pour obtenir tous les articles
    Route::get('/', [ArticleController::class,'show'])->name('articles.show');

    // Route pour obtenir les articles disponibles en fonction d'une disponibilité (oui/non)
    Route::get('/disponibles', [ArticleController::class,'index'])->name('articles.disponibles');

    // Route pour obtenir un article spécifique par ID
    Route::get('/{id}', [ArticleController::class,'viewArticleById'])->name('articles.viewById');

    // Route pour obtenir un article spécifique par libellé (POST)
    Route::post('/libelle', [ArticleController::class,'viewArticleByLibelle'])->name('articles.viewByLibelle');

    // Route pour mettre à jour un article spécifique par ID
    Route::patch('/{id}', [ArticleController::class,'updateArticleById'])->name('articles.updateById');

    // Route pour mettre à jour un article spécifique par stock (POST)
    Route::post('/stock', [ArticleController::class,'updateArticleByStock'])->name('articles.updateByStock');

    // // Route pour supprimer un article spécifique par ID
    Route::delete('/{id}', [ArticleController::class,'destroy'])->name('articles.destroy');
});