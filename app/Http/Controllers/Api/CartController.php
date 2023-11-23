<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShoppingCart;
use App\Models\Product;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller //Controlador carrito
{
    // Agregar al carrito..
    public function addToCart(Request $request, $id) {
        // Validaciones
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'productId' => 'required|exists:products,id'
        ]);
        //
        //$user = auth()->user();
        $user = User::find($id);
        $this->createCart($user -> id);
        $cart = $user->shoppingCart;
        $product = Product::findOrFail($request->productId);

        // Validar stock
        if ($product->stock == 0) {
            return response()->json(['message' => 'El producto está agotado'], 422);
        }
    
        // Validar cantidad deseada
        if ($request->quantity > $product->stock) {
            return response()->json(['message' => 'La cantidad deseada es mayor al stock disponible'], 422);
        }
        //Precio del producto
        $price = $product->promo ? $product->pricePromo : $product->price;

        // Validar si ya existe una entrada para el producto en el carrito
        $pivot = $cart->products()->where('product_id', $request->productId)->first();

        // Actualizar o crear la entrada en la tabla pivote
        if ($pivot) {
            // Si ya existe, actualizar la cantidad y recalcular el subtotal
            $pivot->pivot->quantity = $request->quantity;
            $pivot->pivot->subtotal = $price * $request->quantity;
            $pivot->pivot->save();
        } else {
            // Si no existe, crear una nueva entrada
            $cart->products()->attach($request->productId, [
                'quantity' => $request->quantity,
                'subtotal' => $price * $request->quantity
            ]);
        }
        // Actualizar el Total_cost en el carrito
        
        $this->updateTotalCost($user->shoppingCart);

        return response()->json(['message' => 'Producto agregado al carrito con éxito']);
    }

    // Actualizar carrito
    /*public function updateCart(Request $request) {
        // Validaciones del request
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'productId' => 'required|exists:products,id'
        ]);
        
        $user = auth()->user();
        $product = Product::findOrFail($request->productId);

        // Validar stock
        if ($product->stock == 0) {
            return response()->json(['message' => 'El producto está agotado'], 422);
        }
    
        // Validar cantidad deseada
        if ($request->quantity > $product->stock) {
            return response()->json(['message' => 'La cantidad deseada es mayor al stock disponible'], 422);
        }
    
        // Validar si el producto está en el carrito
        $pivot = $user->shoppingCart->products()->where('product_id', $product->id)->first();
    
        if ($pivot) {
            // Si el producto está en el carrito, actualiza la cantidad
            $pivot->pivot->quantity = $request->quantity;
            $pivot->pivot->save();
        } else {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }
    
        // Actualizar el Total_cost en el carrito después de la actualización de cantidad
        $this->updateTotalCost($user->shoppingCart);
    
        return response()->json(['message' => 'Carrito actualizado con éxito', 'costo' => $user->shoppingCart->total_cost]);
    }*/
    //Eliminar productos del carrito
    public function deleteCartItem(Request $request, $id) {
        // Validaciones
        $request->validate([
            'productId' => 'required|exists:products,id'
        ]);
    
        //$user = auth()->user();
        $user = User::find($id);
        $productId = $request->productId;
    
        // Verificar si el producto está en el carrito
        //$pivot = $user->shoppingCart->products()->where('product_id', $productId)->first();
        $pivot = $user->shoppingCart->products()->where('product_id', $request->productId)->first();

        
    
        if ($pivot) {
            // Si el producto está en el carrito, eliminarlo
            $user->shoppingCart->products()->detach($productId);
            
    
            // Actualizar el Total_cost en el carrito después de la eliminación
            $this->updateTotalCost($user->shoppingCart);
    
            return response()->json(['message' => 'Producto eliminado del carrito con éxito', 'costo' => $user->shoppingCart->total_cost]);
        } else {
            // El producto no estaba en el carrito, puedes manejar esto según tus necesidades
            return response()->json(['message' => 'El producto no estaba en el carrito'], 422);
        }
    }
    //Mostrar info del carrito
    public function showCartInfo($id) {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }
        
        $cart = $user->shoppingCart;

        if (!$cart) {
            return response()->json(['message' => 'El usuario no tiene un carrito asociado'], 404);
        }

        //if ($this->recentChanges($cart)) {
            // Si hay cambios, llamar a updateTotalCost para actualizar el precio total
            $this->updateTotalCost($cart);
        //}
        
        $carrito = ShoppingCart::where('user_id', $user->id)->with('user')->first();

        // Obtener información detallada del carrito
        $cartDetails = $cart->products->map(function ($product) {
            return [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->promo ? $product->pricePromo : $product->price,
                'quantity' => $product->pivot->quantity,
                'subtotal' => $product->pivot->subtotal,
            ];
        });

    
        return response()->json(["carrito" => $carrito, "productos"=> $cartDetails]);
    }
    
    
    //Crear carrito
    public function createCart($userId){
        // Buscar al usuario por su ID
        $user = User::find($userId);

        // Verificar si el usuario existe
        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        // Verificar si el usuario ya tiene un carrito
        if (!$user->shoppingCart) {
        // Si no tiene un carrito, crea uno
            $shoppingCart = new ShoppingCart();
            $user->shoppingCart()->save($shoppingCart);

            return response()->json(['message' => 'Carrito de compras creado con éxito'], 201);
        } else {
         // Si ya tiene un carrito, puedes manejar esto según tus requerimientos
            return response()->json(['message' => 'El usuario ya tiene un carrito de compras'], 422);
        }
    }
    
    

    private function updateTotalCost(ShoppingCart $cart) {
        $cart->update([
            'total_cost' => $cart->products->sum('pivot.subtotal')
        ]);
    }
    

}
