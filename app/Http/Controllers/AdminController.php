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
            "status" => 1,
            "data" => $recipes
        ];
    }
    
    public function destroy(Request $request, $id){
        
        $recipe->body = $request->body;
        $recipe->save();
        return response()->json([
            'status' => 'success',
            'message' => 'Todo updated successfully',
            'todo' => $todo,
        ]);
    }

}