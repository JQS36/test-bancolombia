<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Managers\CartsManager;

class CartsController extends Controller
{

    public function __construct(
        private readonly CartsManager $cartManager
    ){}

    public function addProduct(Request $request){
        $productData = $request->validate([
            'external_id'  => 'string|max:32|required',
            'qty'    => 'required|numeric|min:1|max:1000',
        ]);
        $user = $request->user();
        $cart = $this->cartManager->getOrCreateCartByUser($user);
        $product = $this->cartManager->getProduct($productData["external_id"]);
        if (is_null($product)){
            return response()->json(["State"=>0, "Answer"=>"Product no found", "User"=>$user->name]);   
        }
        if ($productData["qty"] > $product["stock"]){
            return response()->json(["State"=>0, "Answer"=>"Quantity is greather than stock available, Stock available is: {$product["stock"]}", "User"=>$user->name]);
        }
        $this->cartManager->saveCartProducts($cart->id, $product->id, $productData['qty']);
        return response()->json(["State"=>1, "Answer"=>"Add product successfully", "User"=>$user->name, "Cart"=>$cart->id]);
    }

    public function getProducts(Request $request){
        $user = $request->user();
        $cart = $this->cartManager->getIncompleteCart($user);
        if ($cart === null) {
            return response()->json(["State" => 0, "Answer" => "User has not active cart, please add products first"]);
        }
        $products = $this->cartManager->getProducts($cart->id);
        return response()->json(["cart"=>["cartId"=>$cart->id, "cartEmail"=>$cart->email], "products"=>count($products) > 0 ? $products : "N/A"]);
    }

    public function checkout(Request $request){
        $user = $request->user();
        $cart = $this->cartManager->getIncompleteCart($user);
        if ($cart === null) {
            return response()->json(["State"=>0, "Answer"=>"User has not active cart, please add products first", "User"=>$user->name]);
        }
        $products = $this->cartManager->getProducts($cart->id);
        $total    = $this->cartManager->getTotal($products);
        $this->cartManager->complete($cart, $products);
        return response()->json(["State" => 1, "cart" => $cart->id, "email"=>$cart->email,"total"=>$total, "products"=>$products]);
    }

    public function deleteProduct(Request $request, string $identifierId){
        $user = $request->user();
        $cart = $this->cartManager->getIncompleteCart($user);
        if ($cart === null) {
            return response()->json([
                "State"=>0,
                "Answer"=>"User has not active cart, please add products first",
                "User"=>$user->name
            ]);
        }
        $productAdded = $this->cartManager->getProductAdded($cart, $identifierId);
        if ($productAdded === null) {
            return response()->json([
                "State" => 0,
                "Answer" => "Product doesnt exist on the cart",
                "User" => $user->name
            ]);
        }
        $this->cartManager->deleteProduct($productAdded);
        return response()->json([
            "State" => 1,
            "Answer" => "Removed product"
        ]);
    }
}