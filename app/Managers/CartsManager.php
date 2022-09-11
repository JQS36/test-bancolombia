<?php

namespace App\Managers;
use App\Models\User;
use App\Models\Cart;
use App\Models\CartProducts;
use App\Models\products;

class CartsManager{

    public function getOrCreateCartByUser(User $user){
        return Cart::query()->firstOrCreate([
            'email'        => $user->email,
            'is_completed' => false
        ]);
    }

    public function getProduct(string $identifier_id){
        $product = products::query()
            ->where('identifier_id', $identifier_id)
            ->first();
        return $product;
    }

    public function saveCartProducts(int $cartId, int $productId, int $quantity){
        CartProducts::updateOrCreate([
            'cart_id'    => $cartId, 
            'product_id' => $productId],
            ['quantity'   => $quantity]
        );
    }
    

    public function addProduct(Cart $cart, products $product, int $quantity){
        return  CartProducts::query()
            ->select('cart_products.*')
            ->join('products', 'products.id', '=', 'product_id')
            ->where('cart_id', $cart->id)
            ->where('products.identifier_id', $product["identifier_id"])
            ->first();
    }
    public function getIncompleteCart(User $user){
        return Cart::query()
            ->where('email', $user->email)
            ->where('is_completed', false)
            ->first();
    }

    public function getProducts($cartId){
        return CartProducts::query()
            ->select('products.id', 'products.name', 'products.identifier_id', 'products.price', 'products.currency_code', 'cart_products.quantity')
            ->join('products', 'products.id', '=', 'product_id')
            ->where('cart_id', $cartId)
            ->get();
    }

    public function getTotal($products){
        $total = '0';
        foreach ($products as $product) {
            $subtotal = $product['price'] * $product['quantity'];
            $total += $subtotal;
        }
        return round($total,2);
    }

    public function complete(Cart $cart, $products){
        $this->checkout($cart);
        $this->updateProductsStock($products);
    }

    public function checkout(Cart $cart){
        $cart->is_completed = true;
        $cart->save();
    }

    public function updateProductsStock($products){
        foreach ($products as $product) {
            $productToUpdate = products::query()->find($product['id']);
            $productToUpdate->stock = $productToUpdate->stock - $product['quantity'];
            $productToUpdate->save();
        }
    }

    public function getProductAdded(Cart $cart, string $identifierId){
        return CartProducts::query()
            ->select('cart_products.*')
            ->join('products', 'products.id', '=', 'product_id')
            ->where('cart_id', $cart->id)
            ->where('products.identifier_id', $identifierId)
            ->first();
    }

    public function deleteProduct(CartProducts $productAdded){
        $productAdded->delete();
    }
    
}