<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function create(Request $request){
        $request->validate([
            'username' => 'required|string',
            'name' => 'required|string',
            'lastName' => 'required|string',
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        // Crear el producto
        $newUser = new User();
        $newUser->username = $request->username;
        $newUser->name = $request->name;
        $newUser->lastName = $request->lastName;
        $newUser->email = $request->email;
        $newUser->password = $request->password;
        $newUser->rolAdmi = false;
        $newUser->save();

        return response()->json(['message' => 'Usuario creado con éxito'], 200);
    }
}
