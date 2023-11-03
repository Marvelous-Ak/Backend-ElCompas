<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function store(Request $request)
{
    $data = $request->all();

    // Validar los datos si es necesario
    $validator = Validator::make($data, [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8',
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    // Crear un nuevo usuario
    $user = User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => bcrypt($data['password']),
    ]);

    return redirect()->route('users.index')->with('success', 'Usuario creado correctamente');
}

public function edit($id)
{
    $user = User::findOrFail($id);
    return view('users.edit', compact('user'));
}

public function update(Request $request, $id)
{
    $data = $request->all();

    // Validar los datos si es necesario
    $validator = Validator::make($data, [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,'.$id,
        'password' => 'sometimes|string|min:8',
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    // Actualizar el usuario
    $user = User::findOrFail($id);
    $user->name = $data['name'];
    $user->email = $data['email'];
    if(isset($data['password'])){
        $user->password = bcrypt($data['password']);
    }
    $user->save();

    return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente');
}

public function destroy($id)
{
    $user = User::findOrFail($id);
    $user->delete();

    return redirect()->route('users.index')->with('success', 'Usuario eliminado correctamente');
}
}
