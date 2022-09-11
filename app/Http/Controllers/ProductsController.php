<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\products;

class ProductsController extends Controller
{
    public function getProducts(){
        return response()->json([
            "State" => 1,
            "Products" => products::all()
        ],200);
    }

    public function getProduct($identifier){
        $product = products::query()->where("identifier_id", $identifier)->first();
        if(is_null($product)){
            return response()->json([
                "State" => 0,
                'Msg' => 'Product not found'
            ],404); 
        }
        return response()->json([ "State"=>1, "Product"=>$product],200);
    }

    public function addProduct(Request $request){
        try {
            $request->validate([
                'name' => 'string|required',
                'identifier_id'  => 'string|max:32|required'
            ]);
            $product = products::create($request->all());
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
