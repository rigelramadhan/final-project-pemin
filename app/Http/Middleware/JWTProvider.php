<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Firebase\JWT\JWT;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Http\Request;

class JWTProvider
{
    public static function jwt($user) {
        return JWT::encode(
            [
                'sub' => $user->email,
                'iss' => 'http://localhost:8080',
                'aud' => 'http://localhost:8080',
                'iat' => time(),
                'exp' => time() + 60 * 60,
                'role' => $user->role,
            ],
            env('JWT_KEY', 'secret'),
            'HS256'
        );
    }
}