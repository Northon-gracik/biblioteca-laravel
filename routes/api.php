<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GeneroController;
use App\Http\Controllers\LivroController;
use App\Http\Controllers\EmprestimoController;

// Rota de teste da API
Route::get('/', function () {
    return response()->json([
        'message'   => 'API Sistema de Biblioteca funcionando!',
        'version'   => '1.0.0',
        'timestamp' => now(),
    ]);
});

// Rotas para usuários
Route::apiResource('users', UserController::class);

// Rotas para gêneros
Route::apiResource('generos', GeneroController::class);

// Rotas para livros
Route::apiResource('livros', LivroController::class);
Route::get('livros-disponiveis', [LivroController::class, 'disponiveis']);

// Rotas para empréstimos
Route::apiResource('emprestimos', EmprestimoController::class);
Route::get('emprestimos-atrasados', [EmprestimoController::class, 'atrasados']);
Route::patch('emprestimos/{id}/devolver', [EmprestimoController::class, 'devolver']);
Route::patch('emprestimos/{id}/marcar-atrasado', [EmprestimoController::class, 'marcarAtrasado']);

// Rota para informações da API
Route::get('info', function () {
    return response()->json([
        'api'         => 'Sistema de Biblioteca',
        'version'     => '1.0.0',
        'description' => 'API para gerenciamento de biblioteca com usuários, livros e empréstimos',
        'endpoints'   => [
            'users'      => 'Gerenciamento de usuários',
            'generos'    => 'Gerenciamento de gêneros de livros',
            'livros'     => 'Gerenciamento de livros',
            'emprestimos'=> 'Gerenciamento de empréstimos',
        ],
    ]);
});