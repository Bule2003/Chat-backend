<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;

/*Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});*/

Broadcast::channel('chat.{conversationId}', function ($user, int $conversationId) {
    /*return Auth::check();*/
    return true;
}, ['guards' => ['api']]);

