<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\products;
use App\Managers\ProductsManager;

class ProductsController extends Controller
{

    public function __construct(
        private readonly ProductsManager $productsManager
    ){}

    public function getProducts(){
        $products = $this->productsManager->getAllProducts();
        return response()->json([
            "State" => 1,
            "Msg" => "All products",
            "Products" => $products
        ],200);
    }

    public function getProduct(string $identifierId){
        $product = $this->productsManager->getProduct($identifierId);
        if(is_null($product)){
            return response()->json([
                "State" => 0,
                'Msg' => 'Product not found'
            ],404); 
        }
        return response()->json([ 
            "State" => 1,
            "Product" => [
                "name" => $product->name,
                "Asin" => $product->identifier_id,
                "Stock" => $product->stock,
                "Price" => $product->price]
        ],200);
    }

    public function addProduct(Request $request){
        try {
            $request->validate([
                'name' => 'string|required',
                'identifier_id'  => 'string|max:32|required'
            ]);
            $product = $this->productsManager->createProduct($request);
        } catch (\Throwable $th) {
            return ["Fix"=>$th->getMessage()];
        }
        return response([
            "State" => 1,
            "answer" => "Saved correctly",
            "product" => $product], 200);
    }

    public function updateProduct(Request $request, $id){
        $product = products::query()->where("identifier_id",$id);
        if(is_null($product)){
            return response()->json([
                "State" => 0,
                'Msg'=> 'Register not found'
            ],404); 
        }
        $product->update($request->all());
        return response([
            "State"=> 1,
            "answer"=>"Update successfully",
            "product"=>$product->first()], 200);
    }
    
    public function deleteProduct($identifier){
        $product = products::query()->where("identifier_id", $identifier)->delete();
        return response([
            "State"=> 1,
            "answer"=>"Deleted successfully asin: {$identifier}"
        ], 200);
    }

}
