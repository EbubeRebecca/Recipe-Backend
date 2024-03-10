<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Recipe;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller{
    public function index(){
        $category = Category::latest()->paginate();
        return response()->json([
            'success'=> True,
            'message' => 'Category list',
            'category' => $category
        ]);
    }
    public function distinct_cat(){
        $distinctLocation = Recipe::select('location')->distinct()->get();

        return response()->json([
            'success'=> True,
            'message' => 'Distinct locations',
            'locations' => $distinctLocation,
        ]);
    }
}