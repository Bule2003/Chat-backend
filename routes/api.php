<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\SessionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Resources\MessageResource;

/*Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');*/

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout')->middleware('auth:api');
    Route::post('refresh', 'refresh')->middleware('auth:api');
});

Route::post('conversations', [ConversationController::class, 'create'])->middleware('auth:api');

Route::middleware('auth:api')->group(function () {
    Route::post('SendMessage', [ConversationController::class, 'SendMessage']);
    Route::get('conversations', [ConversationController::class, 'index']);
    Route::get('messages/{conversation}', [MessageController::class, 'index']);
    Route::get('conversations/{conversation}', [ConversationController::class, 'show']);
});

Route::get('load', [MessageController::class, 'load']);
