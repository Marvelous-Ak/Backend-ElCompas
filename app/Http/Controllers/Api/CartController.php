<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShoppingCart;
use App\Models\Product;

class CartController extends Controller //Controlador carrito
{
    // Agregar al carrito..
    public function addToCart(Request $request) {
        // Validaciones
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'productId' => 'required|exists:products,id'
        ]);
        //
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

        // Obtener la relación pivot para el producto en el carrito
        $pivot = $user->shoppingCart->products()->where('product_id', $product->id)->first();

        if ($pivot) {
            // Si el producto ya está en el carrito, actualiza la cantidad
            $pivot->pivot->quantity += $request->quantity;
            $pivot->pivot->save();
        } else {
            // Si el producto no está en el carrito, agrégalo
            $user->shoppingCart->products()->attach($request->productId, ['quantity' => $request->quantity]);
        }

        // Actualizar el Total_cost en el carrito
        $this->updateTotalCost($user->shoppingCart);

        return response()->json(['message' => 'Producto agregado al carrito con éxito', 'costo' => $user->shoppingCart->total_cost]);

    
        /*/ Agregar producto al carrito
        $user->shoppingCart->products()->attach($request->productId, ['quantity' => $request->quantity]);

        //Precio del producto
        $price = $product->promo ? $product->pricePromo : $product->price;

        // Actualizar el Total_cost en el carrito
        $total = $user->shoppingCart->update([
            'total_cost' => $user->shoppingCart->products->sum(function ($product) use ($price){
                return $price * $product->pivot->quantity;
            })
        ]);

        return response()->json(['message' => 'Producto agregado al carrito con éxito', 'costo' => $total ]);*/
    }

    // Actualizar carrito
    public function updateCart(Request $request) {
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
    }
    //Eliminar productos del carrito
    public function deleteCartItem(Request $request) {
        // Validaciones
        $request->validate([
            'productId' => 'required|exists:products,id'
        ]);
    
        $user = auth()->user();
        $productId = $request->productId;
    
        // Verificar si el producto está en el carrito
        $pivot = $user->shoppingCart->products()->where('product_id', $productId)->first();
    
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
    public function showCartInfo() {
        $user = auth()->user();
        $cart = $user->shoppingCart;
        // Obtener información detallada del carrito
        $cartDetails = $cart->products->map(function ($product) {
            return [
                'product_id' => $product->id,
                'name' => $product->name,
                'quantity' => $product->pivot->quantity,
                'price' => $product->promo ? $product->pricePromo : $product->price,
                'subtotal' => ($product->promo ? $product->pricePromo : $product->price) * $product->pivot->quantity,
            ];
        });
    
        // Calcular el costo total del carrito
        $totalCost = $cart->total_cost;
    
        return response()->json(['cart_details' => $cartDetails, 'total_cost' => $totalCost]);
    }
    
    
    

    private function updateTotalCost(ShoppingCart $cart) {
        $cart->update([
            'total_cost' => $cart->products->sum(function ($product) {
                //Precio del producto
                $price = $product->promo ? $product->pricePromo : $product->price;
                return $price * $product->pivot->quantity;
            })
        ]);
    }
    

}
