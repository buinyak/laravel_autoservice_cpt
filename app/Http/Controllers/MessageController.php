<?php

namespace App\Http\Controllers;

use App\Events\New_chat;
use App\Models\Chat;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{

    public function new_chat (Request $request){

        $valid = $request->validate([
            'email' => ['required', 'string']
        ]);
        $receiver = User::where('email',$request->email)->first();
        if(Auth::id()==$receiver->id){
            return response()->json(["new_chat"=>[
                'error' => "Неправильный id"
            ]]);
        }
        $user_id = Auth::id();

        if (!$receiver){
            return response()->json(["new_chat"=>[
            'error' => "Пользователь с таким email не найден"
        ]]);}


        if(!Chat::select('id')->where(['creator_id' => $receiver->id, 'receiver_id' => $user_id])->first() && !Chat::select('id')->where(['creator_id' => $user_id, 'receiver_id' => $receiver->id])->first()){
            Chat::factory()->create([
                'creator_id' => $user_id,
                'receiver_id' => $receiver->id
            ]);
            $new_chat = Chat::select()->where([['creator_id',$user_id],['receiver_id',$receiver->id]])->first();
            \App\Events\New_chat::dispatch($new_chat);
        }else {
            return response()->json(["new_chat"=>[
                'error' => "Чат уже создан"
            ]]);
        }

        return response()->json(["new_chat"=>[
            'email' => $request->email,
            'user' => $receiver
        ]]);
    }
    public function load_chats(){
        $user_id = Auth::id();
        $chats = Chat::where('receiver_id',$user_id)
            ->orWhere('creator_id',$user_id)
            ->with('messages:chat_id,message,sender_name,created_at,updated_at')
            ->get();

        foreach($chats as $chat){
            if($chat->receiver_id != $user_id){
                $chat->with = User::select('id','name','email')->where('id',$chat->receiver_id)->first();
            }else {
                $chat->with = User::select('id','name','email')->where('id',$chat->creator_id)->first();
            }
            unset($chat->receiver_id);
            unset($chat->creator_id);
        }
        return response()->json(["chats"=>$chats]);
    }

    public function new_message(Request $request){
        $check = Chat::where('id',$request->chat_id)->first();
        if ($check->receiver_id == Auth::id() || $check->creator_id == Auth::id()){
            \App\Events\PrivateChat::dispatch($request->all());

            $user_id = Auth::id();
            $valid = $request->validate([
                'chat_id' => ['required', 'integer'],
                'message' => ['required','string']
            ]);

            Message::create([
                'chat_id' => $request->chat_id,
                'message' => $request->message,
                'sender_name' => Auth::user()->name
            ]);
            return $valid;
        }
        return response()->json(["error"=>"invalid chat id"]);
    }
    /*public function chat_delete(Request $request){
        $valid = $request->validate([
            'id' => ['required', 'integer']]);
        if(Chat::select()->where([['id',$request->id],['receiver_id',Auth::id()]])->orWhere([['id',$request->id],['creator_id',Auth::id()]])){
            Chat::where('id',$request->id)->delete();
            return 'chat deleted';
        }else{
            return response()->json([
                'error' => 'Неправильный id'
            ]);
        }
    }*/
}
