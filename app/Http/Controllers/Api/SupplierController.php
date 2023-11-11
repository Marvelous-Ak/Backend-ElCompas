<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    //Crear
    public function create(Request $request){
        $data = $request->all(); 

        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'lastName' => 'required|string',
            'email' => 'required|string|email|max:255|unique:suppliers',
            'phone' => 'required|numeric|digits:10',
            'address' => 'required|string|max:255',
            'business' => 'required|string|max:250'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        $new_supplier = Supplier::create([
            'name' => $data['name'],
            'lastName' => $data['lastName'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'business' => $data['business']
        ]);

        return response()->json(['message' => 'Proveedor registrado con éxito'], 200);
    }

    //Mostrar
    public function read($id){
        $supplier = Supplier::findOrFail($id);
        return response()->json($supplier,200);
    }
    //Editar 
    public function update(Request $request, $id){
        $data = $request->all(); 
        $supplier = Supplier::findOrFail($id);

        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'lastName' => 'required|string',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('suppliers', 'email')->ignore($supplier->id), // usuario actual
            ],
            'phone' => 'required|numeric|digits:10',
            'address' => 'required|string|max:255',
            'business' => 'required|string|max:250'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Editar información de proovedor
        $phone= $data['phone'];
        //$formatted_phone = substr($phone, 0, 3) . ' ' . substr($phone, 3, 3) . ' ' . substr($phone, 6);
        $supplier->name = $data['name'];
        $supplier->lastName = $data['lastName'];
        $supplier->email = $data['email'];
        $supplier->phone = $phone;//$formatted_phone;
        $supplier->address = $data['address'];
        $supplier->business = $data['business'];
        $supplier->save();
   
        return response()->json(['message' => 'Información del proovedor actualizado correctamente'], 200);
    }

    //Eliminar
    public function delete($id){
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();
        return response()->json(['message' => 'Proovedor eliminado correctamente'], 200);
    }

    //Mostrar todos los proovedores
    public function showAll(){
        $proveedores = Supplier::all();
        return response()->json($proveedores, 200);
    }
}
