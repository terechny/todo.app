<?php

namespace App\Services;

use App\Models\Task;

class SearchService{

    public function search($request){

        if( $request->has('key') ){

            $tasks = Task::with('tags')->where('title', 'LIKE', '%'. $request->key .'%')->get();

            return response()->json(['data' => $tasks ]);
                       
        } 

        // return response()->json(['data' => {} ]);
    }

}

