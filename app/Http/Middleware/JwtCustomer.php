<?php

namespace App\Http\Middleware;

use App\Enum\ResponseStatusCode;
use App\Exceptions\JsonApiException;
use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtCustomer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    protected $privateKey = "
-----BEGIN RSA PRIVATE KEY-----
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
-----END RSA PRIVATE KEY-----";

    protected $publicKey = "
-----BEGIN PUBLIC KEY-----
MIICITANBgkqhkiG9w0BAQEFAAOCAg4AMIICCQKCAgBe+Jc0dcusLXo7oNidz/6b
XB3oi1X9+2FTsdxDVFZH8bMT3SAiafDuq//irvr4Gq9YVztVfhY1KoBRxI8l2ZcJ
viDcf2uEUTYnWUJsdg7oQRdsTbiOf/inTeMBUQTVwXgZAVH+2vZxKCcjsN4g6ZeS
k6fgTVQkN1ZWdyCT00fxVyuhcAlmPS0cENUpgAFEdY7StGvNiOtrQ5ta6j6cbHcj
8ABcvvxCmdu47KnBh3PKQcKZXEIAHWhmEdZXdEIsF16U19AyefxHUvajZBpULaaU
XwW+1pcD/bZkvFNfVlmyyF3hVloQ/ux4t4ey8eo3eAG5Z76Ueht4qPSW8kp06C+r
NgYOL5KdLpgYXkmDCGDe8HOcYqXXuoOfoxkrirjSH/9BfyRSJQKt6VMW2vtoWvec
Ij2sROmNw1fBIwIl9vuUX0UHwuq167XGTR9/ztI0a64hgzj1d6rH0cVsBgw2O6a0
P0bhY/M8Xvznc8se0JTx95Pbe6bhgKNZ4Fx+JTKJbUotcLI552Ch3ABgH/YcoUyq
eJvKXhlfxBc/trN2HtaO5WzQvWr1lnc+7WeeW58z9D8hmBM9ZxjVOQ2JUmmyW9lM
/pGv9cu3I3BUQx51W0xLtnS9mcyOOCOzGeNKQ7Xr8gxrJjgf0ef/VaZE4s2gxN+j
EqcCUikvKLNT5Sow1zJoNwIDAQAB
-----END PUBLIC KEY-----";

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken() ?? $request->cookie('auth_token');
        if (!$token) {
            throw new JsonApiException('No found token', ResponseStatusCode::PARAMS_INVALID);
        }

        $decoded = $this->verifyToken($token);
        if (isset($decoded['error'])) {
            throw new JsonApiException($decoded['error'], ResponseStatusCode::PARAMS_INVALID);
        }
        $request->attributes->add(['jwt_data' => $decoded['data'] ?? []]);

        return $next($request);
    }

    public function generateToken($data)
    {
        $now = time(); // thời điểm hiện tại
        $midnight = strtotime('tomorrow midnight');
        $payload = [
            'iss' => 'example.org',
            'aud' => 'example.com',
            'iat' => $now,
            'exp' => $midnight,
            'data' => $data
        ];

        return JWT::encode($payload, $this->privateKey, 'RS256');
    }

    public function verifyToken($jwt)
    {
        try {
            $decoded = JWT::decode($jwt, new Key($this->publicKey, 'RS256'));
            return (array) $decoded;
        } catch (\Exception $e) {
            return ['error' => 'Token không hợp lệ hoặc đã hết hạn', 'message' => $e->getMessage()];
        }
    }
}
