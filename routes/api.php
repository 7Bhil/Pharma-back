<?php

use App\Http\Controllers\Api\Admin\PharmacieCreateController;
use App\Http\Controllers\Api\Visiteur\InscriptionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentification Sociale (Google, etc.) - Publique
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    #Obtenir l'URL de redirection OAuth en JSON (pour frontend / SPA)
    Route::get('{provider}/url', [InscriptionController::class, 'getUrl'])->name('socialite.url');

    #Redirection directe par le backend (pour navigateur)
    Route::get('{provider}/redirect', [InscriptionController::class, 'redirect'])->name('socialite.redirect');

    #Callback après confirmation du provider
    Route::get('{provider}/callback', [InscriptionController::class, 'callback'])->name('socialite.callback');
});

#Alias pour compatibilité directe avec les URLs raccourcies
Route::get('redirect/{provider}', [InscriptionController::class, 'redirect']);
Route::get('callback/{provider}', [InscriptionController::class, 'callback']);

/*
|--------------------------------------------------------------------------
| Routes Protégées (Sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    #Informations du profil connecté (Visiteur, Pharmacien ou Admin)
    Route::get('/user', [InscriptionController::class, 'me']);
    Route::get('/me', [InscriptionController::class, 'me']);

    #Déconnexion
    Route::post('/logout', [InscriptionController::class, 'logout']);

    #Espace Administrateur
    Route::middleware('role:Admin')->prefix('administrateur')->group(function () {
        Route::get('/all/pharmacies', [PharmacieCreateController::class, 'index']);
        Route::get('only/pharmacie/{id}', [PharmacieCreateController::class, 'show']);
        Route::post('create/pharmacie', [PharmacieCreateController::class, 'createPharmacie']);
        Route::delete('sup/pharmacie/{pharmacie}', [PharmacieCreateController::class, 'deletePharmacie']);
    });
});