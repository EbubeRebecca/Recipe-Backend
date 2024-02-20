<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipe;
use Illuminate\Support\Facades\Auth;
class AdminController extends Controller{
    public function index()
    {

        $recipes = Recipe::latest()->paginate(10);
        return [
            "success" => True,
            "data" => $recipes
        ];
    }
    
    public function destroyRecipe(Request $request, $id){
        
       
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

    public function userData(Request $request){
        $chef_count = User::where('user_role','chef').count();
        $normal_user = User::where('normal_user','normal_user').count();
        return response()->json([
            'success' => True,
            'message' => 'User data summary',
            'data' => [
                'chef_count'=>$chef_count,
                'normal_user'=>$normal_user
            ]
        ]);
    }

    

}