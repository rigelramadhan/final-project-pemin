<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Http\Request;

class AdminAuthorization
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $guard = null)
    {
        $token = $request->header('authorization') ?? $request->query('authorization');
        if (!$token) {
            return response()->json([
                'success' => false  ,
                'message' => 'Token required.'
            ], 401);
        }

        [
            $header_base64url,
            $payload_base64url,
            $signature_base64url
        ] = preg_split('/\./', $token);

        $header = JWTProvider::base64url_decode($header_base64url);
        $json_header = json_decode($header);

        if (!$json_header->alg || $json_header->alg !== 'HS256') {
            return response()->json([
                'success' => false  ,
                'message' => 'Token algorithm not valid.'
            ], 401);
        }

        if (!$json_header->typ || $json_header->typ !== 'JWT') {
            return response()->json([
                'success' => false  ,
                'message' => 'Token type not valid.'
            ], 401);
        }

        $payload = JWTProvider::base64url_decode($payload_base64url);
        $json_payload = json_decode($payload);
        if (!$json_payload->sub) {
            return response()->json([
                'success' => false  ,
                'message' => 'Token body not valid.'
            ], 401);
        }

        $verified = JWTProvider::verify($signature_base64url, $header_base64url, $payload_base64url, 'SECRET');
        if (!$verified) {
            return response()->json([
                'success' => false  ,
                'message' => 'Token signature not valid.'
            ], 401);
        }

        [$id, $email] = preg_split('/\:/', $json_payload->sub);
        $user = User::where('id', $id)->where('email', $email)->first();
        if (!$user) {
            return response()->json([
                'success' => false  ,
                'message' => 'Token not valid.'
            ], 401);
        }

        if ($user->role == 'admin') {
            $request->user = $user;
            return $next($request);
        } else {
            return response()->json([
                'success' => false  ,
                'message' => 'Unauthorized access.'
            ], 401);
        }
    }
}