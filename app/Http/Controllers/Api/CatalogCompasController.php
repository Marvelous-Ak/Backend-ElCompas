<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Catalog;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CatalogCompasController extends Controller
{
    public function index($id_categoria){
        $categoria = Catalog::find($id_categoria);
        $productos= $categoria->products;

        return response()->json($productos, 200);
    }
    public function showAll(){
        $productos = Product::all();
        return response()->json($productos, 200);
    }
    public function showOne($id_p){
        $producto = Product::find($id_p);
        return response()->json($producto, 200);
    }
    public function create(Request $request){
        $request->validate([
            'brand' => 'required|string',
            'name' => 'required|string',
            'image'=> 'required|string',
            'promo' => 'required|boolean',
            'price' => 'required|numeric',
            'priceAnt' => 'nullable|numeric',
            'stock'=> 'required|integer',
            'categories' => 'required|array', // Se espera un arreglo de categorías
        ]);

        // Crear el producto
        $nuevoProducto = new Product();
        $nuevoProducto->brand = $request->brand;
        $nuevoProducto->name = $request->name;
        $nuevoProducto->image = $request->image;
        $nuevoProducto->promo = $request->promo;
        $nuevoProducto->price = $request->price;
        $nuevoProducto->priceAnt = $request->priceAnt;
        $nuevoProducto->description = $request->description;
        $nuevoProducto->stock = $request->stock;
        $nuevoProducto->save();

        // Asociar el producto a las categorías
        $categories = $request->categories;
        $nuevoProducto->catalogs()->sync($categories); // Asocia las categorías al producto

        return response()->json(['message' => 'Producto creado con éxito'], 200);
    }
    public function update(Request $request, $id_p){
        $productoC = Product::find($id_p);

        $request->validate([
            'brand' => 'required|string',
            'name' => 'required|string',
            'image'=> 'required|string',
            'promo' => 'required|boolean',
            'price' => 'required|numeric',
            'priceAnt' => 'nullable|numeric',
            'stock'=> 'required|integer',
            'categories' => 'required|array', // Se espera un arreglo de categorías
        ]);

        $productoC->fill($request->only([
            'brand','name','image','promo','price','priceAnt','description','stock'
        ]));
  
        $productoC->update($request->all());

        $productoC->catalogs()->sync($request->categories);

        return response()->json(['message' => 'Información del producto actualizado'], 200);
    }
    public function deleteP($id_d){
        $productoD = Product::find($id_d);
        $productoD->delete();
        return response()->json(['message' => 'Producto Eliminado de manera exitosa'], 200);

    }
    public function searchName($name){
        if (strlen($name) < 3) {
            return response()->json(['message' => 'ERROR'], 400);
        }
        $products = Product::where(function ($query) use ($name) {
            $query->where('name', 'LIKE', "%$name%")
                  ->orWhere('description', 'LIKE', "%$name%")
                  ->orWhere('brand', 'LIKE', "%$name%");
        })->get();

        return response()->json($products, 200);
    }

    public function searchName2($name){
        
        // Divide la cadena de búsqueda en palabras
        $searchTerms = explode(' ', $name);
    
        $products = Product::where(function($query) use ($searchTerms) {
            foreach($searchTerms as $term) {
                $query->orWhere('name', 'LIKE', "%$term%")
                      ->orWhere('description', 'LIKE', "%$name%")
                      ->orWhere('brand', 'LIKE', "%$name%");
            }
        })->get();
    
        return response()->json($products, 200);
    }
    
}
