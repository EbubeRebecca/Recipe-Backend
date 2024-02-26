<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AdminController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group(['middleware' => 'api'
],function ($router) {

    Route::get('recipe/count', [RecipeController::class, 'countRecipes']);
}

);
Route::group([],function($router){
    Route::get('account/summary',[AdminController::class, 'userData']);
   
    Route::get('category',[CategoryController::class, 'index']);
    Route::get('recipes/{category_id?}',[RecipeController::class, 'filter_recipe']);
    

});
Route::resource('recipe', RecipeController::class)->middleware('auth');
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/login',  [AuthController::class, 'login'])->name('login');
    Route::post('/admin/login',  [AuthController::class, 'adminLogin'])->name('adminLogin');
    
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);    
});


