<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    public function create(Request $request)
    {
        $valid = $request->validate([
            'event_name' => ['required', 'string'],
            'event_info' => ['required', 'string'],
            'event_type' => ['required', 'string'],
            'event_date' => ['required', 'string'],
            'event_sum' => ['required', 'int']
        ]);
        $valid['user_id']=Auth::user();
        $event = Event::factory()->create($valid);
        return $event;
    }

    public function take(){
        $events = Event::where('user_id', 1)
            ->get();
        return $events;
    }
    public function change(Request $request){
        $valid = $request->validate([
            'id' => ['required', 'integer'],
            'event_name' => ['required', 'string'],
            'event_info' => ['required', 'string'],
            'event_type' => ['required', 'string'],
            'event_date' => ['required', 'string'],
            'event_sum' => ['required', 'int']
        ]);
        if(Event::select()->where([['id',$request->id],['user_id',Auth::id()]])){
            Event::where('id',$request->id)->update([
                'event_name' => $request->event_name,
                'event_info' => $request->event_info,
                'event_type' => $request->event_type,
                'event_date' => $request->event_date,
                'event_sum' => $request->event_sum]);
            return $valid;
        }else {
            return response()->json([
                'error' => 'Неправильный id'
            ]);
        }
    }
    public function delete(Request $request){
        $valid = $request->validate([
            'id' => ['required', 'integer']]);
        if(Event::select()->where([['id',$request->id],['user_id',Auth::id()]])){
            Event::where('id',$request->id)->delete();
            return 'deleted';
        }else{
            return response()->json([
                'error' => 'Неправильный id'
            ]);
        }
    }

}
