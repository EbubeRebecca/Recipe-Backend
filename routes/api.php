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
    Route::post('image/upload',[RecipeController::class, 'upload_image']);
    Route::post('edit/recipe/{id?}/',[RecipeController::class, 'update']);
    
}

);
Route::group([],function($router){
    Route::get('account/summary',[AdminController::class, 'userData']);
    Route::get('category',[CategoryController::class, 'index']);
    Route::get('locations',[RecipeController::class, 'distinct_locations']);
    Route::get('recipes/{category_id?}',[RecipeController::class, 'filter_recipe']);

    Route::get('search',[RecipeController::class, 'search_recipe']);
    Route::get('srecipe/{slug?}',[RecipeController::class, 'slug_show']);
    Route::get('me/recipes',[RecipeController::class, 'personal_recipes']);
    Route::get('user/{user_id}/recipes',[RecipeController::class, 'user_recipes']);

    

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

//Admin urls

Route::group([
    'middleware' => 'admin',
], function ($router) {
    Route::delete('/admin/recipe/{id?}/',  [AdminController::class, 'destroyRecipe']);
   

});


