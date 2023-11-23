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
            'email' => 'required|string|email|max:255|unique:suppliers',
            'phone' => 'required|numeric|digits:10',
            'address' => 'nullable|string|max:255',
            'business' => 'required|string|max:250|unique:suppliers'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'message' => 'F'], 400);
        }
        $new_supplier = Supplier::create([
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
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('suppliers', 'email')->ignore($supplier->id), // usuario actual
            ],
            'phone' => 'required|numeric|digits:10',
            'address' => 'nullable|string|max:255',
            'business' => [
                'required',
                'string',
                'max:250',
                Rule::unique('suppliers', 'business')->ignore($supplier->id), // usuario actual
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Editar información de proovedor
        $supplier->email = $data['email'];
        $supplier->phone = $data['phone'];
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

    // nombres
    public function business() {
        $nombresDeNegocios = Supplier::pluck('business')->toArray();
        return response()->json(['business' => $nombresDeNegocios]);
    }
    
}
