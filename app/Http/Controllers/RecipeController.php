<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipe;
use Illuminate\Support\Facades\Auth;
class RecipeController extends Controller
{

    public function __construct(){
        $this->middleware('auth:api', ['except' => ['index', 'show']]);
    }
    public function index()
    {

        $recipes = Recipe::latest()->paginate(10);
        return [
            'success'=> True,
            "data" => $recipes
        ];
    }


    public function show(Recipe $recipe)
    {
        return [
            'success'=> True,
            "data" =>$recipe
        ];
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'body' => 'required',
            'category_id' => 'required',
            'location'=>'required'
        ]);
        


        $recipe = new Recipe;
        $recipe->title = $request->title;
        $recipe->body = $request->body;
        $recipe->category_id = $request->category_id;
        $recipe->location = $request->location;

        $recipe->created_by_id = Auth::user()->id;
        $recipe->save();



        foreach ($request->file('images') as $imagefile) {
            $image = new Image;
            $path = $imagefile->store('/images/resource');
            $image->url = $path;
            $image->recipe_id = $recipe->id;
            $image->save();
          }
       // $recipe = Recipe::create($request->all());

        return [
            'success'=> True,
            "data" => $recipe
        ];
    }

    public function update(Request $request, $id){
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ]);
        $user_id = Auth::user()->id;
        
        $recipe = Recipe::where('created_by_id',  $user_id)->where('id',$id).first();

        $recipe->title = $request->title;
        $recipe->body = $request->body;
        $recipe->save();
        

        //$todo->title = $request->title;
        //$todo->description = $request->description;
        //$todo->save();

        return response()->json([
            'success'=> True,
            'message' => 'Recipe updated successfully',
            'recipe' => $recipe,
        ]);
    }

    public function countRecipes(Request $request){
        $count = Recipe::count();

        return response()->json([
            'success'=> True,
            'message'=> 'Recipe count',
            'data'=>['count'=>$count]
        ]);
    }
}


