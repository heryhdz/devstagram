<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Mockery\Generator\StringManipulation\Pass\Pass;

class RegistrarController extends Controller
{
    public function index() 
    {
        return view('auth.registrar');
    }

    public function store(Request $request)
    {
        // dd($request);
        // dd($request->get('username'));

        // Modificar el Request
        $request->merge(['username' => Str::slug($request->username)]);

        // Validacion
        $this->validate($request, [
            'name' =>'required|max:30',
            'username' => 'required|unique:users|min:3|max:25',
            'email' => 'required|unique:users|email|max:60',
            'password' => 'required|confirmed|min:6'
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make( $request->password)
        ]);

        //Autenticar un usuario
        auth()->attempt([
            'email' => $request->email,
            'password' => $request->password
        ]);

        //Redireccionar
        return redirect()->route('posts.index', auth()->user()->username);

    }
}
