<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request) {
        $validator = Validator::make($request->all() , [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string',
            'c_password' => 'required|same:password',
        ]);

        if($validator->fails()){
            return response()->json($validator->messages() , 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('userApi')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ] , 201);
    }

    public function login(Request $request) {
        $validator = Validator::make($request->all() , [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json($validator->messages() , 422);
        }

        $user = User::where('email' , $request->email)->first();

        if(!$user){
            return response()->json('کاربری با این مشخصات وجود ندارد' , 401);
        }

        if(!Hash::check($request->password , $user->password)){
            return response()->json('پسورد اشتباه است' , 401);
        }

        $token = $user->createToken('userApi')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ] , 200);
    }

    public function logout() {
        auth()->user()->tokens()->delete();
        return response()->json('خارج شدید' , 200);
    }
}
