<?php

/**
 * user_roles
 */

use User\Services\UserService;


const VACANCY_NONE = 0;
const VACANCY_LEFT = 1;
const VACANCY_RIGHT = 3;
const VACANCY_ALL = 2;




const USER_ROLE_SUPER_ADMIN = 'super-admin';
const USER_ROLE_ADMIN_GATEWAY = 'user-gateway-admin';
const USER_ROLE_ADMIN_KYC = 'kyc-admin';
const USER_ROLE_ADMIN_SUBSCRIPTIONS_ORDER = 'subscriptions-order-admin';
const USER_ROLE_ADMIN_SUBSCRIPTIONS_PACKAGE = 'subscriptions-package-admin';
const USER_ROLE_ADMIN_SUBSCRIPTIONS_PAYMENT = 'subscriptions-payment-admin';
const USER_ROLE_ADMIN_SUBSCRIPTIONS_WALLET = 'subscriptions-wallet-admin';
const USER_ROLE_ADMIN_SUBSCRIPTIONS_GIFTCODE = 'subscriptions-giftcode-admin';
const USER_ROLE_ADMIN_MLM = 'mlm-admin';
const USER_ROLE_CLIENT = 'client';
const USER_ROLE_HELP_DESK = 'help-desk';
const USER_ROLES = [
    USER_ROLE_SUPER_ADMIN,
    USER_ROLE_ADMIN_GATEWAY,
    USER_ROLE_ADMIN_KYC,
    USER_ROLE_ADMIN_SUBSCRIPTIONS_ORDER,
    USER_ROLE_ADMIN_SUBSCRIPTIONS_PACKAGE,
    USER_ROLE_ADMIN_SUBSCRIPTIONS_PAYMENT,
    USER_ROLE_ADMIN_SUBSCRIPTIONS_WALLET,
    USER_ROLE_ADMIN_SUBSCRIPTIONS_GIFTCODE,
    USER_ROLE_ADMIN_MLM,
    USER_ROLE_CLIENT,
    USER_ROLE_HELP_DESK,
];

if (!function_exists('arrayHasValue')) {
    function arrayHasValue($value, $array)
    {
        if (!is_array($array) )
            return false;
        if ($key = array_search($value, $array) !== false){
            return true;
        }
        return false;
    }
}

if (!function_exists('getPackageGrpcClient')) {
    function getPackageGrpcClient()
    {
        return new \Packages\Services\Grpc\PackagesServiceClient(env('SUBSCRIPTION_GRPC_URL','staging-api-gateway.janex.org:9596'), [
            'credentials' => \Grpc\ChannelCredentials::createInsecure()
        ]);
    }
}

if (!function_exists('getGatewayGrpcClient')) {
    function getGatewayGrpcClient()
    {
        return new \User\Services\Grpc\UserServiceClient(env('API_GATEWAY_GRPC_URL', 'development.dreamcometrue.ai:9595'), [
            'credentials' => \Grpc\ChannelCredentials::createInsecure()
        ]);
    }
}

if (!function_exists('getWalletGrpcClient')) {
    function getWalletGrpcClient()
    {
        return new \Wallets\Services\Grpc\WalletServiceClient(env('SUBSCRIPTION_GRPC_URL','staging-api-gateway.janex.org:9596'), [
            'credentials' => \Grpc\ChannelCredentials::createInsecure()
        ]);
    }
}

if (!function_exists('updateUserFromGrpcServer')) {

    function updateUserFromGrpcServer($input_id): ?\User\Services\Grpc\User
    {
        if(!is_numeric($input_id))
            return null;
        $id = new \User\Services\Grpc\Id();
        $id->setId((int)$input_id);
        try {
            $grpc_user = \User\Services\Grpc\GatewayClientFacade::getUserById($id);
            if(!$grpc_user->getId())
                return null;
            app(UserService::class)->userUpdate($grpc_user);
            return $grpc_user;
        } catch (\Exception $exception) {
            return null;
        }
    }
}

if (!function_exists('updateUserFromGrpcServerByMemberId')) {

    function updateUserFromGrpcServerByMemberId($input_id): ?\User\Services\Grpc\User
    {
        if(!is_numeric($input_id))
            return null;
        $id = new \User\Services\Grpc\Id();
        $id->setId((int)$input_id);
        try {
            $grpc_user = \User\Services\Grpc\GatewayClientFacade::getUserById($id);
            if(!$grpc_user->getId())
                return null;
            app(UserService::class)->userUpdate($grpc_user);
            return $grpc_user;
        } catch (\Exception $exception) {
            return null;
        }
    }
}
