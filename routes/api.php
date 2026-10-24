<?php

use App\Http\Controllers\Api\Admin\PharmacieCreateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->group(function(){
    Route::middleware('role:Admin')->prefix('administrateur')->group(function(){
        Route::get('/all/pharmacies', [PharmacieCreateController::class, 'index']);
        Route::get('only/pharmacie/{id}', [PharmacieCreateController::class, 'show']);
        Route::post('create/pharmacie', [PharmacieCreateController::class, 'createPharmacie']);
        Route::delete('sup/pharmacie/{pharmacie}', [PharmacieCreateController::class, 'deletePharmacie']);
    });
});