<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Map;

class MapController extends Controller
{
    public function take(){
        $user_id = Auth::id();
        $maps = DB::table('maps')
            ->where('user_id', $user_id)
            ->get();
        return $maps;
    }
    public function create(Request $request){
        $user_id = Auth::id();
        $valid = $request->validate([
            'pos1' => ['required', 'string'],
            'pos2' => ['required', 'string'],
            'comment' => ['required', 'string']
        ]);
        $valid['user_id']=$user_id;
        Map::factory()->create($valid);
        return response()->json(["map"=>"add"]);

    }
    public function delete(Request $request){

        if(DB::table('maps')->select('user_id')->where(['id','=',$request->id])==Auth::id()){
            DB::table('maps')
                ->where('id',$request->id)
                ->delete();
            return $this->take();
        }
        return response()->json(["error"=>"Invalid id"]);
    }
}
