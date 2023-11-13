<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;



class UserController extends Controller
{
    public function create(Request $request){

        $data = $request->all(); 

        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'lastName' => 'required|string',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Crear un nuevo usuario
        $user = User::create([
            'name' => $data['name'],
            'lastName' => $data['lastName'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'rolAdmi' => false
        ]);

        return response()->json(['message' => 'Usuario creado con éxito'], 200);
    }
    public function read($id){
        $user = User::findOrFail($id);
        return response()->json($user,200);
    }
    public function update(Request $request, $id){
        $data = $request->all(); 
        $user = User::findOrFail($id);

        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'lastName' => 'required|string',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id), // usuario actual
            ],
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Editar información de usuario
        $user->name = $data['name'];
        $user->lastName = $data['lastName'];
        $user->email = $data['email'];
        if(isset($data['password'])){
            $user->password = bcrypt($data['password']);
        }

        $user->save();
   
        return response()->json(['message' => 'Usuario actualizado correctamente'], 200);
    }
    public function destroy($id){
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'Usuario eliminado correctamente'], 200);
    }
    public function createAdmi(Request $request){

        $data = $request->all(); 

        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'lastName' => 'required|string',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Crear un nuevo usuario
        $user = User::create([
            'name' => $data['name'],
            'lastName' => $data['lastName'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'rolAdmi' => true
        ]);

        return response()->json(['message' => 'Usuario creado con éxito'], 200);
    }
    public function login  (Request $request){
        // Validar credenciales
        $credentials = $request->only('email', 'password');

        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            return response()->json(['error' => 'El usuario no existe'], 404);
        }

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Correo electrónico o contraseña incorrectos'], 401);
        }

        return response()->json(['token' => $token]);
    }
    public function logout(){
    }
}
