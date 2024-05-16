<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InstituicaoController;
use App\Http\Controllers\PetsInstituicaoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function() {
    Route::controller(InstituicaoController::class)->group(function() {
        Route::post('cadastro-pet/{nomeInstituicao}', 'cadastroPet');
    });

    Route::controller(PetsInstituicaoController::class)->group(function() {
        Route::put('atualizar-status-pet', 'atualizarStatusPet');
        Route::delete('excluir-pet/{petId}', 'excluirPet');
    });
});

Route::controller(AuthController::class)->group(function() {
    Route::post('login', 'login');
});

Route::controller(InstituicaoController::class)->group(function() {
    Route::get('instituicoes', 'instituicoes');
    Route::get('pets/{nomeInstituicao}', 'pets');
    Route::post('cadastro', 'cadastro');
    Route::post('cadastro-pet', 'cadastroPet');
});
