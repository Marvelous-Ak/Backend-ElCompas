<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Catalog;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CatalogCompasController extends Controller
{
    public function index($id_categoria){
        $categoria = Catalog::find($id_categoria);
        if (!$categoria) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }
        $productos= $categoria->products;

        return response()->json($productos, 200);
    }
    public function showAll(){
        $productos = Product::all();
        return response()->json($productos, 200);
    }
    public function showOne($id_p){
        $producto = Product::find($id_p);
        if (!$producto) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }
        //$array_suppliers= $producto->suppliers;
        //$name_suppliers = Supplier::whereIn('id', $array_suppliers)->pluck('business');
        //$producto->name_suppliers= $name_suppliers;
        
        return response()->json($producto, 200);
    }
    public function create(Request $request){
        $request->validate([
            'brand' => 'required|string',
            'name' => 'required|string',
            'promo' => 'required|boolean',
            'image'=> 'required',
            'price' => 'required|numeric',
            'pricePromo' => 'nullable|numeric',
            'stock'=> 'required|integer',
            'PDA'=> 'required|numeric',
            'categories' => 'required|array', // Se espera un arreglo de categorías
        ]);

        // Crear el producto
        $nuevoProducto = new Product();
        $nuevoProducto->brand = $request->brand;
        $nuevoProducto->name = $request->name;
        $nuevoProducto->image = $request->image;
        $nuevoProducto->promo = $request->promo;
        $nuevoProducto->price = $request->price;
        $nuevoProducto->pricePromo = $request->pricePromo;
        $nuevoProducto->description = $request->description;
        $nuevoProducto->stock = $request->stock;
        $nuevoProducto->cost_of_sale = $request->PDA;
        $nuevoProducto->save();

        // Asociar el producto a las categorías
        $categories = $request->categories;
        $nuevoProducto->catalogs()->sync($categories); // Asocia las categorías al producto

        return response()->json(['message' => 'Producto creado con éxito', 'status'=> 0], 200);
    }
    public function update(Request $request, $id_p){
        $productoC = Product::find($id_p);

        $request->validate([
            'brand' => 'required|string',
            'name' => 'required|string',
            'image'=> 'required|string',
            'promo' => 'required|boolean',
            'price' => 'required|numeric',
            'pricePromo' => 'nullable|numeric',
            'stock'=> 'required|integer',
            'PDA'=> 'required|numeric',
            'categories' => 'required|array', // Se espera un arreglo de categorías
        ]);


        $productoC->fill($request->only([
            'brand','name','promo','image','price','pricePromo','description','stock'
        ]));
        $productoC->cost_of_sale = $request->PDA;
        
        $productoC->update($request->all());

        $productoC->catalogs()->sync($request->categories);

        return response()->json(['message' => 'Información del producto actualizado'], 200);
    }
    public function deleteP($id_d){
        $productoD = Product::find($id_d);
        try {
            $productoD->delete();
            return response()->json(['message' => 'Producto eliminado de manera exitosa'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar el producto'], 500);
        }
    }
    public function searchProduct($name){
        if (strlen($name) < 3) {
            return response()->json(['message' => 'ERROR'], 400);
        }
        $products = Product::where(function ($query) use ($name) {
            $query->where('name', 'LIKE', "%$name%")
                  ->orWhere('description', 'LIKE', "%$name%")
                  ->orWhere('brand', 'LIKE', "%$name%");
        })->get();
        if ($products->isEmpty()) {
            return response()->json(['message' => 'No se encontraron resultados'], 404);
        }
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
        if ($products->isEmpty()) {
            return response()->json(['message' => 'No se encontraron resultados'], 404);
        }
        return response()->json($products, 200);
    }
    public function showCate($id){
        $producto = Product::find($id);
        $categoria= $producto->catalogs;
        return response()->json($categoria,200);
    }


    //función para actualizar el PDA
    public function PDA(){
        // Verifica si la columna 'promo' es falsa y actualiza 'cost_of_sale' en consecuencia
        Product::where('promo', false)->update([
            'cost_of_sale' => \DB::raw('GREATEST(price - 10, price / 2)')
        ]);

        // Verifica si la columna 'promo' es verdadera y actualiza 'cost_of_sale' en consecuencia
        Product::where('promo', true)->update([
            'cost_of_sale' => \DB::raw('GREATEST(pricePromo - 10, pricePromo / 2)')
        ]);

        return response()->json(['message' => 'update exitosa'], 200);
        
    }

}
