<?php


use Illuminate\Http\Request;
use User\Services\UserService;


/**
 * user_roles
 */
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


if (!function_exists('user')) {

    function user(int $id): ?\User\Services\Grpc\User
    {
        $user_db = \User\Models\User::query()->find($id);

        $user = new \User\Services\Grpc\User();
        $user->setId((int)$user_db->id);
        $user->setFirstName($user_db->first_name);
        $user->setLastName($user_db->last_name);
        $user->setUsername($user_db->username);
        $user->setEmail($user_db->email);
        return $user;

    }
}


if (!function_exists('getUserGrpcServerClient')) {
    function getUserGrpcServerClient()
    {
        return new \User\Services\Grpc\UserServiceClient('staging-api-gateway.janex.org:9595', [
            'credentials' => \Grpc\ChannelCredentials::createInsecure()
        ]);
    }
}
if (!function_exists('updateUserFromGrpcServer')) {

    function updateUserFromGrpcServer($user_id): ?\User\Services\Grpc\User
    {
        if (!is_numeric($user_id))
            return null;

        $id = new \User\Services\Grpc\Id();
        $id->setId((int)$user_id);
        try {
            /** @var $user \User\Services\Grpc\User */
            list($user, $status) = getUserGrpcServerClient()->getUserById($id)->wait();
            if ($status->code == 0) {
                app(UserService::class)->userUpdate($user);
                return $user;
            }
            return null;
        } catch (\Exception $exception) {
            return null;
        }
    }
}
