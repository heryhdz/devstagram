<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class PerfilController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('perfil.index');
    }

    public function store(Request $request)
    {

        // Modificar el Request
        $request->merge(['username' => Str::slug($request->username)]);

        $this->validate($request, [
            'username' => ['required','unique:users,username,'.auth()->user()->id,'min:3','max:25', 'not_in:twitter,editar-perfil'],
        ]);

        if($request->imagen){
            $imagen = $request->file('imagen');

            // Generar un nombre único para la imagen
            $nombreImagen = Str::uuid() . "." . $imagen->extension();

            // Leer la imagen usando Intervention Image
            $imagenServidor = Image::read($imagen);

            // Redimensionar la imagen si es necesario
            $imagenServidor->resize(1000, 1000);

            // Obtener la ruta completa de la imagen guardada
            $imagenPath = public_path('perfiles').'/' . $nombreImagen;

            $imagenServidor->save($imagenPath);
        }

        // Guardar Cambios
        $usuario = User::find(auth()->user()->id);

        $usuario->username = $request->username;
        $usuario->imagen = $nombreImagen ?? auth()->user()->imagen ?? null;
        $usuario->save();

        // Redireccionar al usuario
        return redirect()->route('posts.index', $usuario->username);

    }
}
