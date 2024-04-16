<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Storage;

class ImagenController extends Controller
{
    
    public function store(Request $request)
    {
        $imagen = $request->file('file');

        // Generar un nombre único para la imagen
        $nombreImagen = Str::uuid() . "." . $imagen->extension();

        // Leer la imagen usando Intervention Image
        $imagenServidor = Image::read($imagen);

        // Redimensionar la imagen si es necesario
        $imagenServidor->resize(1000, 1000);

        // Obtener la ruta completa de la imagen guardada
        $imagenPath = public_path('uploads').'/' . $nombreImagen;

        $imagenServidor->save($imagenPath);

        // Devolver la respuesta JSON con la imagen procesada
        return response()->json(['imagen' => $nombreImagen]);
    }
}
