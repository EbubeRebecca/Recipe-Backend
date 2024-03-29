<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipe;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
class RecipeController extends Controller
{

    public function __construct(){
        $this->middleware('auth:api', ['except' => ['index', 'show','filter_recipe']]);
    }
    public function index()
    {

        $recipes = Recipe::latest()->paginate(10);
        return response()->json([
            'success'=> True,
            "data" => $recipes
        ]);
    }

    public function filter_recipe(Request $request)
    {
        if($request->filled('category_id') && $request->filled('location') ){
            $recipes = Recipe::where('category_id',$request->category_id)->where('location',$request->location)->paginate(10);
        }elseif($request->filled('category_id') ){
            $recipes = Recipe::where('category_id',$request->category_id)->paginate(10);
        }elseif($request->filled('location')){
            $recipes = Recipe::where('location',$request->location)->paginate(10);
        }else{

        $recipes = Recipe::latest()->paginate(10);
        }


        return response()->json([
            'success'=> True,
            "data" => $recipes
        ]);

        
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
            'location'=>'required',
           
            
        ]);

        //$file = $request->file('video');
        //info($request->file('imagesß'));


        $recipe = new Recipe;
        $recipe->title = $request->title;
        $recipe->body = $request->body;
        $recipe->category_id = $request->category_id;
        $recipe->location = $request->location;

        $recipe->created_by_id = Auth::user()->id;

       // if ($request->hasFile('video')){
       // $vidfile = $request->file('video');
       // $vidpath =  $request->file('video')->store('/images/resource');



        $vfile = $request->file('video'); // Retrieve the uploaded file from the request
$filename = $vfile->getClientOriginalName(); // Retrieve the original filename

$vidpath = Storage::disk('local')->put('/images/resource'.$filename, file_get_contents($vfile));
        $recipe->video = $vidpath;
    //}
        $recipe->save();



        



        foreach ($request->file('images') as $imagefile) {
            $image = new Image;
            $path = $imagefile->store('/images/resource');
            $image->url = $path;
            $image->recipe_id = $recipe->id;
            $image->save();
          }
       

        return [
            'success'=> True,
            "data" => $recipe
        ];
    }

    public function update(Request $request, $id){
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:255',
        ]);
        
        $user_id = Auth::user()->id;
        
        $recipe = Recipe::where('created_by_id',  $user_id)->where('id',$id);

   
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

    public function countRecipes(Request $request){
        $count = Recipe::count();

        return response()->json([
            'success'=> True,
            'message'=> 'Recipe count',
            'data'=>['count'=>$count]
        ]);
    }
}


