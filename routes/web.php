<?php

use App\Http\Controllers\MessageController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\SessionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

Broadcast::routes(['middleware' => ['auth:api']]);

Route::post('/broadcasting/auth', function () {
    logger('entered');
    return Auth::user();
});

Route::view('/', 'home');
Route::view('/chat', 'chat');

