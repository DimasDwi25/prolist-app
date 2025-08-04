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

// Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });

Broadcast::channel('logs.project.{projectId}', function ($user, $projectId) {
    return true; // atau tambahkan pengecekan user punya akses
});

Broadcast::channel('phc.notifications', function ($user) {
    // Hanya izinkan 4 user tertentu
    return in_array($user->id, [1, 2, 3, 4]); // Ganti dengan ID user target
});

Broadcast::channel('notifications.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});



