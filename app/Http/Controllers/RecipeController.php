<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipe;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
class RecipeController extends Controller
{

    public function __construct(){
        $this->middleware('auth:api', ['except' => ['index', 'show','filter_recipe']]);
    }
    public function index()
    {

        $recipes = Recipe::with('user')->latest()->paginate(10);
        return response()->json([
            'success'=> True,
            "data" => $recipes
        ]);
    }

     

public function random_strings($length_of_string)
{
 
    // String of all alphanumeric character
    $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
 
    // Shuffle the $str_result and returns substring
    // of specified length
    return substr(str_shuffle($str_result), 
                       0, $length_of_string);
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

    public function show($id)
    {
        ///$user = User$recipe->created_by_id
        $recipe = Recipe::with('user')->find($id);
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
            'images'=>'required'
        ]);
        


        $recipe = new Recipe;
        $recipe->title = $request->title;
        $recipe->body = $request->body;
        $recipe->category_id = $request->category_id;
        $recipe->location = $request->location;

        $recipe->created_by_id = Auth::user()->id;


        if ($request->hasFile('video')){
        $vidfile = $request->file('video');
        $vidpath =  $request->file('video')->store('/images/resource');
        $recipe->video = $vidpath;}
        $recipe->save();

        //$path = Storage::putFile('avatars', $request->file('images'));

      

        $file = $request->file('images');
        
        //$path =  $request->file('images')->store('/resource');
        $destinationPath = 'images';
        Log::debug(explode('.',$file->getClientOriginalName())[0]);
        Log::debug($file->getClientOriginalName());
        $local_file_name = substr(explode('.',$file->getClientOriginalName())[0],0,6). $this->random_strings(6).'.'.$file->extension();
        $file->move($destinationPath,$file->getClientOriginalName());

        //$vidfile = $request->file('video');
        //$file->move($destinationPath,$vidfile->getClientOriginalName());

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
            'body' => 'required|string|max:255',
        ]);
        
        $user_id = Auth::user()->id;
        
        $recipe = Recipe::where('created_by_id',  $user_id)->where('id',$id);

        //$recipe->title = $request->title;
        //$recipe->body = $request->body;
        $recipe->update(
            ['title'=>$request->title,
            'body'=>$request->body]
        );
        

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