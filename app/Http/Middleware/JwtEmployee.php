<?php

namespace App\Http\Middleware;

use App\Enum\Authen\AuthenStatusCode;
use App\Exceptions\AuthenException;
use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtEmployee
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
MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEAlocdegoA3qpT2P9cnNUt
nllt34vqB92V3bdU44rYMEtHFzEu1FzEK3wcn+UTVUvB0xBzsT+FEd4yM1cfr4xH
r7InJpJKP4byrmWS3OaHKQMBI1xyFZElLnzaf1cBF+8ThoygIeCb+Jp2fiqY962h
VOTlIQistqzjyuj361J1/1ZX2/8+ZloUMBRn9y/ITi3YeKXqxRt1byyUvKgYjSnc
eShitvH8EbKgHdtr8aXX9K1lJjKeVTc7JqnnYNRR28GPZTy/4imBWMqBYucapRze
re2uwsJVvj0Gi2Z+FTXAZPDwmpcC8ImfPsQtBseds2g8jxd0VnkOXo8v3TT/18im
NJCu5yTwF2Lcp4glcF6h0ZpWhcTlrnWq1z87Nz4nRfzbErgOvuMVLXSjMecn+noF
J8PrhYm4neiUVSG1x1Qd5O1Ucq/bSPKfmPfTe/ccxpxfeIhEvrBRV1/Ukvddpjla
osx0oU0CAvZMQBDXXf38qHDNj4i2ToUc3gQh4MrpUvFomlAkZ8DVLYQB7IY+4bGf
YBhZkyW7MpXSxwNIgloa3zFSPzJ3dLtcQSBe4mQfd7/Oypr5pIg9ymIxNQqannzR
Ccbj8wnwQePHGOiF9q4VofXAI8nLotFAE19cfsowH/zgTOVxHdUj3oT8CBC6WlA1
VdhiE3hySYlGMcae++WtSOMCAwEAAQ==
-----END PUBLIC KEY-----";

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken() ?? $request->cookie('auth_token');
        if (!$token) {
            throw new AuthenException('No found token', AuthenStatusCode::PARAMS_INVALID);
        }

        $decoded = $this->verifyToken($token);
        if (isset($decoded['error'])) {
            throw new AuthenException($decoded['error'], AuthenStatusCode::PARAMS_INVALID);
        }
        $request->attributes->add(['jwt_data' => $decoded['data'] ?? []]);

        return $next($request);
        return $next($request);
    }

    public function generateToken($data)
    {
        $now = time(); // thời điểm hiện tại
        $midnight = strtotime('tomorrow midnight');
        $payload = [
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
