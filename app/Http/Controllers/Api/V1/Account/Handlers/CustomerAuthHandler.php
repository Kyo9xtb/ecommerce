<?php

namespace App\Http\Controllers\Api\V1\Account\Handlers;

use App\Enum\Authen\AuthenStatusCode;
use App\Exceptions\AuthenException;
use App\Helper\CodeHelper;
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
            'register' => $this->handleRegister($command),
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
        );

        return response()->json(
            $this->responseSuccess(
                [
                    'auth-token' => $token,
                    'user' => [
                        'id' => $customer['id'],
                        'customer_code' => $customer['customer_code'],
                        'full_name' => $customer['full_name'],
                        'email' => $customer['email'],
                    ]
                ],
                "Login success"
            )
        )->withCookie($cookie);
    }

    public function handleLogout($command)
    {
        $cookie = cookie()->forget('auth_token');

        return response()->json(
            $this->responseSuccess(
                [],
                "Logout success"
            )
        )->withCookie($cookie);
    }

    public function handleProfile($command)
    {
        $jwtData = $command->request->attributes->get('jwt_data');

        if (empty($jwtData) || empty($jwtData->id)) {
            throw new AuthenException('Token is invalid or missing user information.', AuthenStatusCode::NOT_FOUND);
        }

        $customer = $this->customerInterface->findCustomerById($jwtData->id);

        if (!$customer) {
            throw new AuthenException('No customers found.', AuthenStatusCode::NOT_FOUND);
        }

        if ($customer->customer_code !== $jwtData->customer_code) {
            throw new AuthenException('Customer information does not match.', AuthenStatusCode::UNAUTHORIZED);
        }

        return $this->responseSuccess(
            CustomerResource::make($customer)->resolve(),
            'Get customer information successfully.'
        );
    }

    public function handleRegister($command)
    {
        $inputData = [
            'email' => $command->email,
            'password' => bcrypt($command->password),
            'phone' => $command->phone,
            'full_name' => $command->full_name,
        ];

        if ($this->customerInterface->findCustomerByEmail($command->email)) {
            throw new AuthenException('Email is already in use.', AuthenStatusCode::CONFLICT);
        }

        do {
            $customerCode = CodeHelper::generateCodeNumeric('', 5);
        } while (
            $this->customerInterface->findCustomerByCode($customerCode)
        );

        $inputData['customer_code'] = $customerCode;

        $customer = $this->customerInterface->createCustomer($inputData);

        if (!$customer) {
            throw new AuthenException('Unable to create account. Please try again later.', AuthenStatusCode::SERVER_ERR);
        }

        return $this->responseSuccess(
            CustomerResource::make($customer)->resolve(),
            'Account registration successful.'
        );
    }
}
