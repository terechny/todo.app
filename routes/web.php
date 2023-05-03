<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\SearchController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});



Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {
    
    Route::get('/todo', function () { return view('todo'); });
    Route::get('/api/search', [ SearchController::class, 'search'])->name('search');
    Route::get('/api/search/tag', [ SearchController::class, 'searchTag'])->name('search.tag');
    Route::post('/api/task/update', [ TaskController::class, 'update'])->name('task.update');
    Route::apiResource('/api/task', TaskController::class)->except('update');
    Route::apiResource('/api/tag', TagController::class)->except('update');   
});

