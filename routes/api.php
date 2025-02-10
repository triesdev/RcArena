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
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\PaymentMethodController;
use App\Http\Controllers\API\TicketUserController;
use App\Http\Controllers\Panel\EventController as PanelEventController;
use App\Http\Controllers\Panel\TicketController as PanelTicketController;
use App\Http\Controllers\Panel\FirebaseController;

// Panel
use App\Http\Controllers\Panel\TransactionController as PanelTransactionController;
use App\Http\Controllers\Panel\PaymentMethodController as PanelPaymentMethodController;
use App\Http\Controllers\Panel\PanelClassController;

Route::group(['prefix' => "v1", "middleware" => [ApiToken::class]], function () {

    // Guest Mode
    Route::get("guest/event-home", [EventController::class, "getEventHome"])->withoutMiddleware([ApiToken::class]);

    // Auth
    Route::post("login-sso", [AuthController::class, 'SSOLogin'])->withoutMiddleware([ApiToken::class]);
    Route::post("login", [AuthController::class, "Login"])->withoutMiddleware([ApiToken::class]);
    Route::post("register", [AuthController::class, "Register"])->withoutMiddleware([ApiToken::class]);
    Route::get("auth", [AuthController::class, "Auth"]);
    Route::post("logout", [AuthController::class, "Logout"]);
    Route::post("register-fcm-token", [AuthController::class, "RegisterFcmToken"]);

    // Users
    Route::resource('users', UserController::class);
    Route::get("users/get-by-code/{code}", [UserController::class, "getByCode"]);

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
        Route::post('/handle-calculation-qty-cart', [CartController::class, 'handleCalculationQtyCart']);
        Route::delete('/delete-bundle-ticket/{id}', [CartController::class, 'deleteBundleTicket']);
        Route::delete('/delete-piece-ticket/{id}', [CartController::class, 'deletePieceTicket']);
    });

    // Transaction
    Route::resource("transactions", TransactionController::class);
    Route::get("transactions/detail-payment/{id}", [TransactionController::class, "getTransactionForPayment"]);

    // Ticket User => Transaction Detail Users
    Route::get("user-tickets", [TicketUserController::class, "userTickets"]);
    Route::get("user-tickets/{transaction_detail_users_id}", [TicketUserController::class, "detailTicketUser"]);
    Route::patch("user-tickets/{transaction_detail_users_id}", [TicketUserController::class, "updateParticipantName"]);
    Route::get("user-tickets-by-transaction-id/{transaction_id}", [TicketUserController::class, "userTicketsByTransactionId"]);
    Route::post("user-tickets-transfer", [TicketUserController::class, "transferTicket"]);

    // Payment
    Route::resource("payments", PaymentController::class);

    // Payment Methods
    Route::resource("payment-methods", PaymentMethodController::class);
});

Route::group(['prefix' => "panel", "middleware" => [PanelToken::class]], function () {
    // Auth
    Route::post("login", [PanelAuthController::class, "login"])->withoutMiddleware([PanelToken::class]);
    Route::get("auth", [PanelAuthController::class, "auth"]);
    Route::post("logout", [PanelAuthController::class, "logout"]);
    Route::post("send-notification", [PanelAuthController::class, 'testSendNotification']);

    // Account
    Route::resource("users", UserController::class);
    Route::get("users/get-by-code/{code}", [UserController::class, "getByCode"]);
    Route::resource("roles", RoleController::class);
    Route::resource("menus", MenuController::class);
    Route::resource("menu-role", MenuRoleController::class);
    Route::resource("panel-classes", PanelClassController::class);

    Route::resource("transactions", PanelTransactionController::class);
    Route::patch("transaction-payment-process/{payment_id}", [PanelTransactionController::class, "paymentProcess"]);
    Route::resource("events", PanelEventController::class);

    /*Properties*/
    Route::get('events-properties', [PanelEventController::class, 'properties']);
    Route::get('ticket-properties', [PanelTicketController::class, 'properties']);
    /*End Properties*/

    Route::get("menu-tree", [MenuRoleController::class, "menuTree"]);
    Route::post("update-menu-role", [MenuRoleController::class, "update"]);
    Route::get('roles-list', [RoleController::class, 'list']);
    Route::get('menus-list', [MenuController::class, 'list']);

    Route::get("report/balance-sheet", [ReportController::class, "balanceSheet"]);

    Route::get("firebase-config", [FirebaseController::class, "loadConfig"]);

    /*Payment Methods*/
    Route::resource("payment-methods", PanelPaymentMethodController::class);
});
