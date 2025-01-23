<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Panel\PanelAuthController;

Route::get('/', function () {
    return "ok";
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';

Route::get('/panel', [PanelAuthController::class, 'adminPanel']);
Route::get('/panel/{path}', [PanelAuthController::class, 'adminPanel'])->where('path', '([A-z\d\-\/_.]+)?');

Route::get('/auth/{path}', [PanelAuthController::class, 'authView'])
    ->where('path', '([A-z\d\-\/_.]+)?');
