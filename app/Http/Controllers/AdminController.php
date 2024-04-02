<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
class AdminController extends Controller{
    public function __construct(){
        $this->middleware('admin', ['except' => ['userData']]);
    }
    public function index()
    {

        $recipes = Recipe::latest()->paginate(10);
        return response()->json([
            "success" => True,
            "data" => $recipes
        ]);
    }
    
    public function destroyReciper(Request $request, $id){
        $user_id = Auth::user()->is_admin;
       
        $recipe = Recipe::where('id', $id);


        if (!$recipe) {
            return response()->json([
                'success'=>False,
            'message' => 'Recipe not found'],
             404);
        }
    
        // Delete the item
        $recipe->delete();
        return response()->json([
            'success' => True,
            'message' => 'Recipe deleted by admin',
        ]);
    }

    public function destroyRecipe(Request $request, $id){
      
        //$user_id = Auth::user()->is_admin;
       
        $recipe = Recipe::where('id', $id);


        if (!$recipe) {
            return response()->json([
                'success'=>False,
            'message' => 'Recipe not found'],
             404);
        }
    
        // Delete the item
        $recipe->delete();
        return response()->json([
            'success' => True,
            'message' => 'Recipe deleted by admin',
        ]);
    }
    public function updateRecipe(Request $request, $id){
        $request->validate([
            'title' => 'required|string|max:255',
            
            'body' => 'required|string|max:255',
        ]);
        
        $user_id = Auth::user()->id;
        
        $recipe = Recipe::where('id',$id);

       
        $recipe->update(
            ['title'=>$request->title,
            'body'=>$request->body]
        );
        

       

        return response()->json([
            'success'=> True,
            'message' => 'Recipe updated successfully',
            'recipe' => $recipe,
        ]);
    }


    public function userData(Request $request){
        $chef_count = User::where('user_type','chef')->count();
        $recipe_user_count = User::where('user_type','recipe_seeker')->count();
        $recipe_count = Recipe::count();
        return response()->json([
            'success' => True,
            'message' => 'User data summary',
            'summary' => [
                'chef_count'=>$chef_count,
                'recipe_seeker_count'=>$recipe_user_count,
                'recipe_count'=>$recipe_count
            ]
        ]);
    }

    

}