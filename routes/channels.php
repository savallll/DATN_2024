<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Broadcast::channel('chat.{userId1}.{userId2}', function ($user, $userId1, $userId2) {
//     return $user->id == $userId1 || $user->id == $userId2;
// });
Broadcast::channel('chat.{userId1}.{userId2}', function ($user, $userId1, $userId2) {
    if ($user->id == $userId1 || $user->id == $userId2) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email, 
        ];
    }
    return false;
});
