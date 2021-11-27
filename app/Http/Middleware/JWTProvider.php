<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Http\Request;

class JWTProvider
{
    public static function base64url_encode($data): string {
        $base64 = base64_encode($data);
        $base64url = strtr($base64, '+/', '-_');

        return rtrim($base64url, '=');
    }
    
    public static function base64url_decode(string $base64url): string
    {
        $base64 = strtr($base64url, '-_', '+/');
        $json = base64_decode($base64);

        return $json;
    }

    private static function sign(string $header_base64url, string $payload_base64url, string $secret): string
    {
        $signature = hash_hmac('sha256', "{$header_base64url}.{$payload_base64url}", $secret, true);
        $signature_base64url = JWTProvider::base64url_encode($signature);

        return $signature_base64url;
    }

    public static function verify(string $signature_base64url, string $header_base64url, string $payload_base64url, string $secret): bool
    {
        $signature = JWTProvider::base64url_decode($signature_base64url);
        $expected_signature = JWTProvider::base64url_decode(JWTProvider::sign($header_base64url, $payload_base64url, $secret));

        return hash_equals($expected_signature, $signature);
    }

    public static function jwt(array $header, array $payload, String $secret): String {
        $header_json = json_encode($header);
        $payload_json = json_encode($payload);

        $header_base64url = JWTProvider::base64url_encode($header_json);
        $payload_base64url = JWTProvider::base64url_encode($payload_json);
        $signature_base64url = JWTProvider::sign($header_base64url, $payload_base64url, $secret);

        $jwt = "{$header_base64url}.{$payload_base64url}.{$signature_base64url}";

        return $jwt;
    }
}