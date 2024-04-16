<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class PostController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth')->except(['show', 'index']);
    }

    public function index(User $user)
    {   
        $posts = Post::where('user_id', $user->id)->latest()->paginate(20);

        return view('dashboard',[
            'user' => $user,
            'posts' => $posts
        ]);
    }

    public function create()
    {   
        //Mostrar formulario para guardar la informacion de los posts
        return view('posts.create');

    }

    // Permite validar y guardar la información del formulario post
    public function store(Request $request){
        $this->validate($request,[
            'titulo' => 'required|max:255',
            'descripcion'=> 'required|',
            'imagen' => 'required'
        ]);

        // Post::create([
        //     'titulo' => $request->titulo,
        //     'descripcion' => $request->descripcion,
        //     'imagen' => $request->imagen,
        //     'user_id' => auth()->user()->id
        // ]);

        // Otra forma de guardar datos en el modelo
        // $Post = new Post;
        // $Post->titulo =  $request->titulo;
        // $Post->descripcion = $request->descripcion;
        // $Post->imagen = $request->imagen;
        // $Post->user_id = auth()->user()->id;
        // $Post->save();

        $request->user()->posts()->create([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'imagen'=>$request->imagen,
            'user_id' => auth()->user()->id
        ]);

        return redirect()->route('posts.index', auth()->user()->username);
    }

    public function show(User $user, Post $post)
    {
        return view('posts.show', [
            'post' => $post,
            'user' => $user
        ]);
    }

    public function destroy(Post $post)
    {
       $this ->authorize('delete', $post);
       $post->delete();

       // Eliminar la imagen 

       $imagen_path = public_path('uploads/'. $post->imagen);

       if(File::exists($imagen_path)){
            unlink($imagen_path);
       }

       return redirect()->route('posts.index', auth()->user()->username);

    }
}
