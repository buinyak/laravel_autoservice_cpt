<?php

use App\Models\Chat;
use Illuminate\Support\Facades\Auth;
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


Broadcast::channel('chat.{chat_id}', function ($user, $chat_id) { // $user,
    $check = Chat::where('id',$chat_id)->first();
    if ($check->receiver_id == Auth::id() || $check->creator_id == Auth::id()){
        return (int) true;
    }
    return (int) false;
});
Broadcast::channel('new_chat.{user_id}', function ($user, $user_id) { // $user,
    if (Auth::user()->id == $user_id){
        return (int) true;
    }
    return (int) false;
});
