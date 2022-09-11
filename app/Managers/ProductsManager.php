<?php

namespace App\Managers;
use Illuminate\Http\Request;
use App\Models\products;

class ProductsManager{

    public function getAllProducts(){
        return products::all();
    }

    public function getProduct(string $identifierId){
        return products::query()->where("identifier_id", $identifierId)->first();
    }

    public function createProduct(Request $request){
        return products::create($request->all());
    }
}