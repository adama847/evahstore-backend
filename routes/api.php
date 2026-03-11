<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\Api\ProductController;

// ===== Auth =====
Route::post('/auth/login', [AdminAuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout',          [AdminAuthController::class, 'logout']);
    Route::post('/auth/change-password', [AdminAuthController::class, 'changePassword']);

    // ===== Products =====
    Route::post('/products',             [ProductController::class, 'store']);      // Créer un produit
    Route::put('/products/{product}',    [ProductController::class, 'update']);     // Modifier un produit
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);    // Supprimer un produit
    Route::get('/stats',                 [ProductController::class, 'stats']);      // Statistiques produits
});

// ===== Public Products =====
Route::get('/products',           [ProductController::class, 'index']);  // Liste des produits
Route::get('/products/{product}', [ProductController::class, 'show']);   // Détails produit