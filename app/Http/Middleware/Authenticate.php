<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Exception;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Authenticate
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
        $header = $request->header('authorization') ?? $request->query('authorization');
        if (!$header) {
            return response()->json([
                'success' => false  ,
                'message' => 'Token required.'
            ], 401);
        }

        $token = Str::of($header)->ltrim('Bearer')->trim();

        try {
            $payload = JWT::decode($token, env('JWT_KEY', 'secret'), ['HS256']);
        } catch(ExpiredException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token expired.'
            ], 401);
        } catch(Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }

        $user = User::where('email', $payload->sub)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ], 404);
        }

        $request->user = $user;
        return $next($request);
    }
}