<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use App\Models\Task;
use App\Models\User;

class TaskService
{
    
    public function index(){

        $task = User::findOrFail(auth()->user()->id)
                    ->tasks()                   
                    ->with(['tags'])
                    ->get();

        return response()->json([ 'data' => $task ]);
    }

    public function store($request){

        if( !auth()->check()){ return; }
               
        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => auth()->user()->id
        ]);

        $file = $this->saveFiles($request);

        if( $file ){

            $task->image = $file;
            $task->save();
        }

        return response()->json([ 'data' => $task ]);        
    }

    public function show($id){

        return response()->json([ 'data' => Task::with('tags')->findOrFail($id) ]);
    }

    public function update($request){

        $task = Task::findOrFail( $request->id );

        $task->title = $request->title;
        $task->description = $request->description;

        $file = $this->saveFiles($request);

        if ($file) {

            $task->image = $file;
            
        }else{

            if( $request->has('deleteimage') ){
             
                $this->deleteImage($task->image );
    
                $task->image = null;
            }
        } 

        $task->save();

        return response()->json([ 'data' => $task ]);        
    }

    public function destroy($id){

        $task = Task::findOrFail($id);

        if( $task->image ){

            $this->deleteImage( $task->image );
        }

        $res = $task->delete();

        return response()->json(['data' => $res ]);        
    }    

    private function deleteImage($fileName){

        $preview =  storage_path('app/public/image/preview/') . $fileName;
        $original =  storage_path('app/public/image/original/') . $fileName;

        if( file_exists( $original ) ){

            unlink($original);
        }

        if(file_exists( $preview ) ){

            unlink( $preview );
        }  
    } 

    private function saveFiles($request){

        if ($request->hasFile('image')) {
                      
            // Save original
            $imagePath = Storage::disk('public')->putFile('image/original', $request->file('image'));
              
            // Save preview
            Image::make($request->file('image')->path())
            ->resize(150, 150, function ($constraint) { $constraint->aspectRatio(); })
            ->save( storage_path('app/public/image/preview') . '/'. $request->file('image')->hashName() ); 

            return $request->file('image')->hashName();
                 
        }else{

            return null;
        } 
    }    
    
}