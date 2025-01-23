<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Panel\AuthController;

Route::get('/', function () {
    return 'OK';
});

Route::apiResource('/test', AuthController::class);

Route::get('/adm/{path}', [AuthController::class, 'Home'])
    ->where('path', '([A-z\d\-\/_.]+)?');
Route::get('/auth/{path}', [AuthController::class, 'Home'])
    ->where('path', '([A-z\d\-\/_.]+)?');
