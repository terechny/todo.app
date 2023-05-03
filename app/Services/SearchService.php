<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;

class SearchService{

    public function search($request){

        if( $request->has('key') ){

            $tasks = Task::with('tags')->where('title', 'LIKE', '%'. $request->key .'%')->get();

            return response()->json(['data' => $tasks ]);
                       
        } 

    }

    public function searchTag($request){

        $params = explode(',', $request->key);
        
        $task = User::findOrFail(auth()->user()->id)
                     ->tasks()
                     ->whereHas('tags', function ($query) use ( $params ){ $query->whereIn('tag', $params); })                     
                     ->with(['tags'])
                     ->get();
 
        return response()->json(['data' => $task ]);
    }

}

