<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channel Routes
|--------------------------------------------------------------------------
|
| Here you can register all of the event broadcasting channels that your
| application supports. The channel authorization callbacks are used
| to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('ngo-event.{eventId}', function ($user, $eventId) {
    // RETURN TRUE if $user is allowed to listen for this event.
    // Replace this with your real permission check. Example:
    //  - $user->isNGO() if you implemented such helper,
    //  - or check $user->role === 'ngo' or ownership of the event.
    //
    // Example fallback returning true for any authenticated user:
    return $user !== null;

    // Better example (adapt to your app):
    // return $user && $user->isNGO();
});

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    // ensure only the correct user can listen on their private channel
    return (int) $user->id === (int) $id;
});