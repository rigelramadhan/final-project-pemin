<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Http\Request;

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

        $header = $this->base64url_decode($header_base64url);
        $json_header = json_decode($header);

        if (!$json_header->alg || $json_header->alg !== 'HS256') {
            return response()->json([
                'success' => false  ,
                'message' => 'Token not valid.'
            ], 401);
        }

        if (!$json_header->typ || $json_header->typ !== 'JWT') {
            return response()->json([
                'success' => false  ,
                'message' => 'Token not valid.'
            ], 401);
        }

        $payload = $this->base64url_decode($payload_base64url);
        $json_payload = json_decode($payload);
        if (!$json_payload->sub) {
            return response()->json([
                'success' => false  ,
                'message' => 'Token not valid.'
            ], 401);
        }

        $verified = $this->verify($signature_base64url, $header_base64url, $payload_base64url, 'SECRET');
        if (!$verified) {
            return response()->json([
                'success' => false  ,
                'message' => 'Token not valid.'
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

        $request->user = $user;
        return $next($request);
    }

    private function base64url_encode($data): string {
        $base64 = base64_encode($data);
        $base64url = strtr($base64, '+/', '-_');

        return rtrim($base64url, '=');
    }
    
    private function base64url_decode(string $base64url): string
    {
        $base64 = strtr($base64url, '-_', '+/');
        $json = base64_decode($base64);

        return $json;
    }

    private function sign(string $header_base64url, string $payload_base64url, string $secret): string
    {
        $signature = hash_hmac('sha256', "{$header_base64url}.{$payload_base64url}", $secret, true);
        $signature_base64url = $this->base64url_encode($signature);

        return $signature_base64url;
    }

    private function verify(string $signature_base64url, string $header_base64url, string $payload_base64url, string $secret): bool
    {
        $signature = $this->base64url_decode($signature_base64url);
        $expected_signature = $this->base64url_decode($this->sign($header_base64url, $payload_base64url, $secret));

        return hash_equals($expected_signature, $signature);
    }
}
