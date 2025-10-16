<?php

namespace App\Http\Controllers\Api\V1\Account\Handlers;

use App\Enum\Authen\AuthenStatusCode;
use App\Exceptions\AuthenException;
use App\Http\Middleware\JwtCustomer;
use App\Http\Resources\CustomerResource;
use App\Http\Responses\Account\CustomerAuthResponse;
use App\Repositories\Customer\CustomerInterface;
use App\Trait\ResponseJson;

class CustomerAuthHandler
{
    use ResponseJson;

    public function __construct(
        public JwtCustomer $jwtCustomer,
        public CustomerInterface $customerInterface,
    ) {}

    public function handle($command)
    {
        $action = $command->request->segment(count($command->request->segments()));
        return match ($action) {
            'login' => $this->handleLogin($command),
            'logout'   => $this->handleLogout($command),
            'me'   => $this->handleProfile($command),
            default  => throw new AuthenException('Method not supported', AuthenStatusCode::PARAMS_INVALID),
        };
    }

    public function handleLogin($command)
    {
        $customer  = $this->customerInterface->loginCustomer($command->email, $command->password);

        if (!$customer) {
            throw new AuthenException(
                'Invalid email or password',
                AuthenStatusCode::UNAUTHORIZED
            );
        }

        $now = time();
        $exp = strtotime('tomorrow midnight');

        $data = [
            'id' => $customer['id'],
            'customer_code' => $customer['customer_code'],
            'full_name' => $customer['customer_code'],
            'email' => $customer['email'],
            'iat' => $now,
            'exp' => $exp,
        ];

        $token = $this->jwtCustomer->generateToken($data);

        $minutes = max(1, ($exp - $now) / 60);

        $cookie = cookie(
            'auth_token',
            $token,
            $minutes,
            '/',     // path
            null,    // domain
            false,   // secure = true nếu dùng HTTPS
            true,    // httpOnly
            false,   // raw
            'Lax'    // SameSite
        );

        return response()->json(
            $this->responseSuccess(
                (new CustomerResource($customer))->resolve(),
                "Login success"
            )
        )->withCookie($cookie);
    }

    public function handleLogout($command)
    {
        $cookie = cookie('auth_token', '', -1, '/', null, true, true);
        return response()->json([
            CustomerAuthResponse::from([
                'message' => "Logout success",
            ])
        ])->withCookie($cookie);
    }

    public function handleProfile($command)
    {
        $jwtData = $command->request->attributes->get('jwt_data');

        if (empty($jwtData?->id)) {
            throw new AuthenException('Invalid token data', AuthenStatusCode::NOT_FOUND);
        }

        $customer = $this->customerInterface->findCustomerById($jwtData->id);

        if (!$customer || $customer->customer_code !== $jwtData->customer_code) {
            throw new AuthenException('Customer not found or mismatched', AuthenStatusCode::UNAUTHORIZED);
        }

        return CustomerAuthResponse::from([
            'message' => 'Create contact customer success',
            'data' => (new CustomerResource($customer))->resolve(),
        ]);
    }
}
