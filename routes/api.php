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

/*Route::get('/register', [RegisteredUserController::class, 'create']);
Route::post('/register', [RegisteredUserController::class, 'store']);*/

// TODO: add login middleware

/*Route::get('/login', [SessionController::class, 'create']);
Route::post('/login', [SessionController::class, 'store']);
Route::post('/logout', [SessionController::class, 'destroy']);*/

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout')->middleware('auth:api', ['except' => ['login','register']]);
    Route::post('refresh', 'refresh')->middleware('auth:api', ['except' => ['login','register']]);
});

Route::resource('messages', MessageController::class);

// TODO: getting all messages for a given user and sending messages
/*Route::post('SendMessage', ConversationController::class);
Route::get('load', MessageController::class);*/

Route::resource('conversations', ConversationController::class);
