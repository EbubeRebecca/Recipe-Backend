<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipe;
use App\Models\Image;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
class RecipeController extends Controller
{

    public function __construct(){
        $this->middleware('auth:api', ['except' => ['index', 'show','filter_recipe','distinct_locations']]);
    }
    public function index()
    {

        $recipes = Recipe::with('user')->with('images')->latest()->paginate(12);
      
        return response()->json([
            'success'=> True,
            "data" => $recipes
        ]);
    }

    public function distinct_locations(){
        $distinctLocation = Recipe::select('location')->distinct()->get();

        return response()->json([
            'success'=> True,
            'message' => 'Distinct locations',
            'locations' => $distinctLocation,
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
            $recipes = Recipe::where('category_id',$request->category_id)->where('location',$request->location)->with('user')->with('images')->paginate(10);
        }elseif($request->filled('category_id') ){
            $recipes = Recipe::where('category_id',$request->category_id)->with('user')->with('images')->paginate(10);
        }elseif($request->filled('location')){
            $recipes = Recipe::where('location',$request->location)->with('user')->with('images')->paginate(10);
        }else{

        $recipes = Recipe::latest()->with('user')->with('images')->paginate(12);
        }


        return response()->json([
            'success'=> True,
            "data" => $recipes
        ]);

        
    }
    public function search_recipe(Request $request){
        $keyword = $request->query('q');
        $reci = Recipe::where('title', 'like', '%' . $keyword . '%')->orWhere('body', 'like', '%' . $keyword . '%')->with('user')->with('images')
             ->paginate(10);
       
        return response()->json([
            'success'=> True,
            "data" => $reci
        ]);
    }

    public function show($id)
    {
        //Get recipe and associated user object
        ///$user = User$recipe->created_by_id
        $recipe = Recipe::with('user')->with('images')->find($id);
        return [
            'success'=> True,
            "data" =>$recipe
        ];
    }

    public function slug_show(Request $request)
    {
        //Get recipe with slug
        ///$user = User$recipe->created_by_id
        $recipe =Recipe::where('slug',$request->slug)->with('user')->with('images')->firstOrFail();
//        $recipe = Recipe::with('user')->find($id);
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
            'images.*' => 'image|mimes:jpeg,png,jpg,gif', // Adjust maximum file size as needed
            'video' => 'file|mimes:mp4,mov,avi',
        ]);
        


        $recipe = new Recipe;
        $recipe->title = $request->title;
        $recipe->body = $request->body;
        $recipe->category_id = $request->category_id;
        $recipe->location = ucfirst($request->location);

        $recipe->created_by_id = Auth::user()->id;


        if ($request->hasFile('video')){
            Log::debug('video exists');
        $vidfile = $request->file('video');
        $vidpath =  $request->file('video')->store('videos', 'public');
        $recipe->video = $vidpath;}
        $recipe->save();


        if ($request->hasFile('images')){
            
            foreach ($request->file('images') as $image) {
                $path = $image->store('images', 'public');

                $image = new Image;
                $image->url = $path;
                $image->recipe_id = $recipe->id;
                $image->save();
            }
        }
        return [
            'success'=> True,
            "data" => $recipe
        ];
    }



    public function upload_image(Request $request)
    {
        $request->validate([
            'image' => 'image|mimes:jpeg,png,jpg,gif', // Adjust maximum file size as needed
            
        ]);
        $url='';

        if ($request->hasFile('image')){
            
           
                $path = $request->file('image')->store('images', 'public');

                $image = new Image;
                $image->url = $path;
                $image->save();
                $url=$path;
            }
        
        return [
            'success'=> True,
            "data" => $url,
            "url"=> url(asset('storage/'.$url)),
            "uploaded"=> true,
  "fileName"=>"example.jpg",
        ];
    }
    
    public function personal_recipes(Request $request){
//return recipes created by logged in user
        $user_id = Auth::user()->id;
        
        $recipe = Recipe::where('created_by_id',  $user_id)->with('user')->with('images')->paginate(12);
        return response()->json([
            'success'=> True,
            'message' => 'Retrieve loggedin user recipe',
            'recipe' => $recipe,
        ]);
    }
    public function user_recipes(Request $request,$user_id){
        //return recipes created by logged in user
                
                $recipe = Recipe::where('created_by_id',  $user_id)->paginate(12);
                return response()->json([
                    'success'=> True,
                    'message' => 'Retrieve user recipe',
                    'recipe' => $recipe,
                ]);
            }
    public function update(Request $request, $id){
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:255',
            'category_id' => 'required',
            'location'=>'required',
        ]);
        
        $user_id = Auth::user()->id;
        
        $recipe = Recipe::where('created_by_id',  $user_id)->where('id',$id);
        if (!$recipe) {
            return response()->json([
                'success'=> False,
                'message' => 'Recipe does not exist',
            ]);
         }
       
        $recipe->update(
            ['title'=>$request->title,
            'body'=>$request->body,
            'category_id'=>$request->category_id,
            'location'=>$request->location,
            ]
        );
        
      


        if ($request->hasFile('video')){
        $vidfile = $request->file('video');
        $vidpath =  $request->file('video')->store('videos', 'public');
        
        $recipe->update(
            ['video'=>$vidpath,
            ]
        );
    
    
    }

       

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