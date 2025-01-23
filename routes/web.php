<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Panel\AuthController;

Route::get('/', function () {
    return "ok";
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';

Route::get('/panel', [AuthController::class, 'adminPanel']);
Route::get('/panel/{path}', [AuthController::class, 'adminPanel'])->where('path', '([A-z\d\-\/_.]+)?');

Route::get('/auth/{path}', [AuthController::class, 'auth'])
    ->where('path', '([A-z\d\-\/_.]+)?');
