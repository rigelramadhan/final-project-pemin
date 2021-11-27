<?php

namespace App\Http\Controllers;

use App\Http\Middleware\JWTProvider;
use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    
    public function register(Request $request) {
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'password' => 'required'
        ]);

        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Fields cannot be empty.'
            ], 400);
        }

        $email = $request->input('email');
        $emailExists = User::where('email', $email)->first();

        if ($emailExists) {
            return response()->json([
                'success' => false,
                'message' => 'Email has been taken.',
            ], 400);
        }

        $name = $request->input('name');
        $password = Hash::make($request->input('password'));

        $register = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password
        ]);

        if ($register) {
            $token = JWTProvider::jwt(
                [
                    "alg" => "HS256",
                    "typ" => "JWT"
                ],
                [
                    "sub" => "{$register->id}:{$register->email}",
                    "name" => $register->name,
                    "iat" => time()
                ],
                "Secret"
            );

            $register->token = $token;
            $register->save();

            return response()->json([
                'success' => true,
                'message' => "User has been registered.",
                'data' => ['token' => $token]
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'User failed to register.',
                'data' => ''
            ], 400);
        }
    }

    public function login(Request $request) {
        $validation = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);

        if ($validation->fails()) {
            return response()->json([
                'success' => false  ,
                'message' => 'Username and password cannot be empty.'
            ], 400);
        }

        $email = $request->input('email');
        $password = $request->input('password');

        $user = User::where('email', $email)-> first();

        if ($user) {
            if (Hash::check($password, $user->password)) {
                $token = JWTProvider::jwt(
                    [
                        "alg" => "HS256",
                        "typ" => "JWT"
                    ],
                    [
                        "sub" => "{$user->id}:{$user->email}",
                        "name" => $user->name,
                        "iat" => time()
                    ],
                    "SECRET"
                );

                $user->token = $token;
                $user->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Login successful',
                    'data' => ['token' => $token]
                ], 200);
            } else {
                return response()->json([
                    'success' => false  ,
                    'message' => 'Username and password don\'t match.'
                ], 400);
            }
        } else {
            return response()->json([
                'success' => false  ,
                'message' => 'User not found.'
            ], 404);
        }
    }
}
