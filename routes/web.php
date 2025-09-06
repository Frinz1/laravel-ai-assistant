<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\WebhookController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Auth
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Webhook (outside CSRF)
Route::post('/webhook/stripe', [WebhookController::class, 'handle'])
    ->name('webhook.stripe')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// Plans (auth)
Route::middleware('auth')->group(function () {
    Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');
    Route::post('/plans/subscribe', [PlanController::class, 'subscribe'])->name('plans.subscribe');
    Route::post('/plans/cancel', [PlanController::class, 'cancel'])->name('plans.cancel');
    Route::post('/plans/downgrade', [PlanController::class, 'downgrade'])->name('plans.downgrade');
    Route::get('/plans/success', [PlanController::class, 'success'])->name('plans.success');
});

// Chat UI
Route::middleware('auth')->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
});

// API (auth)
Route::middleware('auth')->prefix('api')->group(function () {
    Route::post('/chat/send', [ChatController::class, 'sendMessage']);
    Route::get('/chat/conversation/{id}', [ChatController::class, 'getConversation']);
    Route::delete('/chat/conversation/{id}', [ChatController::class, 'deleteConversation']); // NEW
}); // The missing closing brace was added here.

Route::middleware('auth')->get('/pkt/{topic}', [ChatController::class, 'downloadPkt'])
    ->name('pkt.download');

// Admin
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.delete');

    Route::get('/conversations', [AdminController::class, 'conversations'])->name('conversations');
    Route::get('/revenue', [AdminController::class, 'revenue'])->name('revenue');
});