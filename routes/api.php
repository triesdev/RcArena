<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\EventController;
use App\Http\Controllers\API\ReportController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\Panel\MenuController;
use App\Http\Controllers\Panel\MenuRoleController;
use App\Http\Controllers\Panel\PanelAuthController;
use App\Http\Controllers\Panel\UserController;
use App\Http\Middleware\ApiToken;
use App\Http\Middleware\PanelToken;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\TicketController;
use App\Http\Controllers\API\CartController;

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
    Route::get('tickets', [TicketController::class, 'getTicketByEventId']);

    // Cart
    Route::group(['prefix' => 'cart'], function () {
        Route::post('add-ticket', [CartController::class, 'addToCarts']);
        Route::get('/', [CartController::class, 'getCarts']);
    });

    // Transaction
    Route::resource("transactions", TransactionController::class);

    Route::resource("roles", RoleController::class);
    Route::resource("menus", MenuController::class);
    Route::resource("menu-role", MenuRoleController::class);
    Route::get("menu-tree", [MenuRoleController::class, "menuTree"]);
});

Route::group(['prefix' => "panel", "middleware" => [PanelToken::class]], function () {
    // Auth
    Route::post("login", [PanelAuthController::class, "login"])->withoutMiddleware([PanelToken::class]);
    Route::get("auth", [PanelAuthController::class, "auth"]);
    Route::post("logout", [PanelAuthController::class, "logout"]);

    // Account
    Route::resource("users", UserController::class);
    Route::resource("roles", RoleController::class);
    Route::resource("menus", MenuController::class);
    Route::resource("menu-role", MenuRoleController::class);
    Route::resource("transactions", TransactionController::class);

    Route::get("menu-tree", [MenuRoleController::class, "menuTree"]);

    Route::get("report/balance-sheet", [ReportController::class, "balanceSheet"]);
});
