<?php

namespace App\Http\Controllers;

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
            $token = $this->jwt(
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
                $token = $this->jwt(
                    [
                        "alg" => "HS256",
                        "typ" => "JWT"
                    ],
                    [
                        "sub" => "{$user->id}:{$user->email}",
                        "name" => $user->name,
                        "iat" => time()
                    ],
                    "Secret"
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

    private function base64url_encode($data): string {
        $base64 = base64_encode($data);
        $base64url = strtr($base64, '+/', '-_');

        return rtrim($base64url, '=');
    }

    private function sign(string $header_base64url, string $payload_base64url, string $secret): string {
        $signature = hash_hmac('sha256', "{$header_base64url}.{$payload_base64url}", $secret, true);
        $signature_base64url = $this->base64url_encode($signature);

        return $signature_base64url;
    }

    private function jwt(array $header, array $payload, String $secret): String {
        $header_json = json_encode($header);
        $payload_json = json_encode($payload);

        $header_base64url = $this->base64url_encode($header_json);
        $payload_base64url = $this->base64url_encode($payload_json);
        $signature_base64url = $this->sign($header_base64url, $payload_base64url, $secret);

        $jwt = "{$header_base64url}.{$payload_base64url}.{$signature_base64url}";

        return $jwt;
    }
}
