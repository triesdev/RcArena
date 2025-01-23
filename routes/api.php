<?php

use App\Http\Controllers\API\AccountController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\MenuController;
use App\Http\Controllers\API\MenuRoleController;
use App\Http\Controllers\API\ReportController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\API\UserController;
use App\Http\Middleware\ApiToken;
use App\Http\Controllers\API\EventController;

Route::group(['prefix' => "v1", "middleware" => [ApiToken::class]], function () {
    // Auth
    Route::post("login-sso", [AuthController::class,'SSOLogin'])->withoutMiddleware([ApiToken::class]);
    Route::post("login", [AuthController::class, "Login"])->withoutMiddleware([ApiToken::class]);
    Route::post("register", [AuthController::class, "Register"])->withoutMiddleware([ApiToken::class]);
    Route::get("auth", [AuthController::class, "Auth"]);
    Route::post("logout", [AuthController::class, "Logout"]);

    // Users
    Route::resource('users', UserController::class);

    /*Events*/
    Route::resource("events", EventController::class);
    Route::get("events-home", [EventController::class, "getEventHome"]);
    Route::get("events-detail/{id}", [EventController::class, "getEventDetail"]);

    // Tickets


    Route::resource("roles", RoleController::class);
    Route::resource("menus", MenuController::class);
    Route::resource("menu-role", MenuRoleController::class);
    Route::get("menu-tree", [MenuRoleController::class, "menuTree"]);
});

//require 'v1-mobile-api.php';
