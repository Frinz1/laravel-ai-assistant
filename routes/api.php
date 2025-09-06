<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;

Route::get('/ping', fn () => response()->json(['status' => 'ok']));

Route::post('/chat/send', [ChatController::class, 'sendMessage']);
Route::get('/chat/conversation/{id}', [ChatController::class, 'getConversation']);
Route::delete('/chat/conversation/{id}', [ChatController::class, 'deleteConversation']);