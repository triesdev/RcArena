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

// Privacy Policy
Route::get('/privacy-policy', function () {
    return view('privacy-policy');
});
Route::get('/faq', function () {
    return view('faq');
});
Route::get('/term-condition', function () {
    return view('term-condition');
});

Route::get('/delete-account', function () {
    return view('delete-account');
});


Route::get('/panel', [PanelAuthController::class, 'adminPanel']);
Route::get('/panel/{path}', [PanelAuthController::class, 'adminPanel'])->where('path', '([A-z\d\-\/_.]+)?');

Route::get('/auth/{path}', [PanelAuthController::class, 'authView'])
    ->where('path', '([A-z\d\-\/_.]+)?');
