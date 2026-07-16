<?php

use App\Models\Post;
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


Broadcast::channel('presence-posts.{postId}', function ($user, $postId) {
    // Returning an array = authorized. This array becomes
    // the "member data" available to everyone else in the channel.
    if (Post::where('id', $postId)->exists()) {
        return [
            'id'   => $user->id,
            'name' => $user->name,
        ];
    }

    // Returning false/null = not authorized, join is rejected
    return false;
});
