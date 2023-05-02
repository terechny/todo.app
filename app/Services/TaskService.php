<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use App\Models\Task;

class TaskService
{
    
    public function index(){

        return response()->json([ 'data' => Task::with('tags')->get() ]);
    }

    public function store($request){

        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description
        ]);

        if ($request->hasFile('image')) {
                      
            // Save original
            $imagePath = Storage::disk('public')->putFile('image/original', $request->file('image'));
              
            // Save preview
            Image::make($request->file('image')->path())
            ->resize(150, 150, function ($constraint) { $constraint->aspectRatio(); })
            ->save( storage_path('app/public/image/preview') . '/'. $request->file('image')->hashName() ); 

            $task->image = $request->file('image')->hashName();
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

        if ($request->hasFile('image')) {

            
            // Save original
            $imagePath = Storage::disk('public')->putFile('image/original', $request->file('image'));
              
            // Save preview
            Image::make($request->file('image')->path())
            ->resize(150, 150, function ($constraint) { $constraint->aspectRatio(); })
            ->save( storage_path('app/public/image/preview') . '/'. $request->file('image')->hashName() ); 

            $task->image = $request->file('image')->hashName();
            
        }else{

            if( $request->has('deleteimage') ){
             
                $this->deleteImage($task->image );
    
                $task->image = null;
            }
        } 

        $task->save();

        return response()->json([ 'data' => $task ]);        
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
    
    public function destroy($id){

        $task = Task::findOrFail($id);

        if( $task->image ){

            $this->deleteImage( $task->image );
        }

        $res = $task->delete();

        return response()->json(['data' => $res ]);        
    }

}