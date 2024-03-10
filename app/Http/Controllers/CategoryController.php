<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller{
    public function index(){
        $category = Category::latest()->paginate();
        return response()->json([
            'success'=> True,
            'message' => 'Category list',
            'category' => $category,
        ]);
    },
    public function distinct_cat(){
        $category = Category::latest()->paginate();
        return response()->json([
            'success'=> True,
            'message' => 'Category list',
            'category' => $category,
        ]);
    }
}