<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Models\Supplier;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class WarehouseController extends Controller
{
    ///agregar nuevo producto a la bodega
    public function addProduct(Request $request){
        $data = $request->all(); 

        $validator = Validator::make($data, [
            'brand' => 'required|string',
            'name' => 'required|string',
            'price' => 'required|numeric',
            'quantity' => 'required|numeric',
            'amount' => 'required|numeric',
            'supplier' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        // Buscar el proveedor por el texto proporcionado
        $supplier = Supplier::where('business', $data['supplier'])->first();

        // Verificar si se encontró el proveedor
        if (!$supplier) {
            return response()->json(['error' => 'Proveedor no encontrado'], 404);
        }
        // Crear el producto
        $nuevoProducto = Warehouse::create([
            'brand' => $data['brand'],
            'name' => $data['name'],
            'price' => $data['price'],
            'quantity' => $data['quantity'],
            'amount' => $data['amount'],
            'supplier_id' => $supplier->id, // Asociar el producto al proveedor
        ]);        

        return response()->json(['message' => 'Producto agregado a la bodega con éxito'], 200);
    }
    //Modificar info de un producto
    public function updateProduct(Request $request, $id){
        $data = $request->all();

        $validator = Validator::make($data, [
            'brand' => 'required|string',
            'name' => 'required|string',
            'price' => 'required|numeric',
            'quantity' => 'required|numeric',
            'amount' => 'required|numeric',
            'supplier' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Buscar el proveedor por el texto proporcionado
        $supplier = Supplier::where('business', $data['supplier'])->first();

        // Verificar si se encontró el proveedor
        if (!$supplier) {
            return response()->json(['error' => 'Proveedor no encontrado'], 404);
        }

        // Buscar el producto por ID
        $producto = Warehouse::find($id);

        // Verificar si se encontró el producto
        if (!$producto) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }

        // Actualizar los atributos del producto
        $producto->update([
            'brand' => $data['brand'],
            'name' => $data['name'],
            'price' => $data['price'],
            'quantity' => $data['quantity'],
            'amount' => $data['amount'],
            'supplier_id' => $supplier->id, // Actualizar la asociación al proveedor
        ]);

        return response()->json(['message' => 'Producto actualizado con éxito'], 200);
    }

    //Modificar unicamente la cantidad
    public function updateQuantity(Request $request, $id){
        $data = $request->validate([
            'outputQuantity' => 'required|integer',
        ]);

        // Buscar el producto por ID
        $producto = Warehouse::find($id);

        // Verificar si se encontró el producto
        if (!$producto) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }

        // Validar que outputQuantity no sea mayor al quantity del producto
        if ($data['outputQuantity'] > $producto->quantity) {
            return response()->json(['error' => 'La cantidad de salida es mayor que la cantidad disponible'], 400);
        }

        // Restar outputQuantity y guardar los cambios
        $producto->quantity -= $data['outputQuantity'];
        $producto->save();

        return response()->json(['message' => 'Cantidad actualizada con éxito'], 200);
    }

    //Eliminar
    public function deleteProduct($id_d){
        $productoD = Warehouse::find($id_d);
        if (!$productoD) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }
        try {
            $productoD->delete();
            return response()->json(['message' => 'Producto eliminado de manera exitosa'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar el producto'], 500);
        }
    }

    //mostrar un producto
    public function showProduct($id){
        $producto = Warehouse::find($id);
        // Verificar si se encontró el producto
        if (!$producto) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }
        // Devolver los datos del producto
        return response()->json($producto, 200);
    }
    //mostrar todos los productos
    public function showAll(){
        $productos = Warehouse::all();
        return response()->json($productos, 200);
    }

}
