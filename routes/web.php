<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'Sistema de Biblioteca - API Laravel',
        'version' => '1.0.0',
        'api_url' => url('/api'),
        'documentation' => 'Consulte o README.md para instruções de uso'
    ]);
});

