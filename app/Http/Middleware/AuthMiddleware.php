<?php

namespace App\Http\Middleware;

use Closure;

use App\Models\User;
class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $token = $request->header('authorization') ?? $request->query('authorization');
        if (!$token) {
            return response()->json([
                'success' => false  ,
                'message' => 'Token required.'
            ], 401);
        }
        $token = explode(' ',$token)[1];
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
        
        $verified = JWTProvider::verify($signature_base64url, $header_base64url, $payload_base64url, 'secret');
        if (!$verified) {
            return response()->json([
                'success' => false  ,
                'message' => 'Token signature not valid.'
            ], 401);
        }
        // [$id, $email] = preg_split('/\:/', $json_payload->sub);
        $user = User::/*where('id', $id)->*/where('email',$json_payload->sub /*$email*/)->first();
        if (!$user) {
            return response()->json([
                'success' => false  ,
                'message' => 'Token not valid.'
            ], 401);
        }

        $request->user = $user;
        return $next($request);
    }
}
