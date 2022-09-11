<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Managers\UsersManager;

class UserController extends Controller
{

    public function __construct(
        private readonly UsersManager $userManager
    ){}

    public function register(Request $request){
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ]);
        $user = $this->userManager->register($request);
        return response()->json([
            "Status" => 1,
            "Answer" => "User Create Successfully", 
            "User" => $user
        ]);
    }

    public function login(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $user = User::where("email", $request->input("email"));
        if ($user->count()){
            $user = $user->first();
            if(Hash::check($request->input("password"), $user->password)){
                //Create to the Token
                $token = $user->createToken("auth_token")->plainTextToken;
                //Yes, all ok,
                return response()->json([
                    "Status" => 1,
                    "Answer" => "User logged in successfully",
                    "acces_token" => $token,
                ]);
            }else{
                return response()->json([
                    "Status" => 0,
                    "Answer" => "Wrong, please verify password"
                ], 404);
            }
        }else{
            return response()->json([
                "Status" => 0,
                "Answer" => "User not registry"
            ], 404);
        }
    }

    public function userProfile(){
        return response()->json([
            "Status" => 1,
            "Answer" => "Perfil User",
            "data" => auth()->user()
            
        ]);
    }

    public function logout(){
        auth()->user()->tokens()->delete();
        return response()->json([
            "Status" => 1,
            "Answer" => "Ended user session"
            
        ]);
    }
    
}