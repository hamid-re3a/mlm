<?php


use Illuminate\Http\Request;
use User\Services\UserService;

if (!function_exists('user')) {

    function user(int $id ) : ?\User\Services\User
    {
        $user_db = \User\Models\User::query()->find($id);

        $user = new \User\Services\User();
        $user->setId((int)$user_db->id);
        $user->setFirstName($user_db->first_name);
        $user->setLastName($user_db->last_name);
        $user->setUsername($user_db->username);
        $user->setEmail($user_db->email);
        return $user;

    }
}


if (!function_exists('updateUserFromGrpcServer')) {
    /**
     * @param Request $request
     * @return array
     */
    function updateUserFromGrpcServer(Request $request): ?\User\Services\User
    {
        $client = new \User\Services\UserServiceClient('staging-api-gateway.janex.org:9595', [
            'credentials' => \Grpc\ChannelCredentials::createInsecure()
        ]);
        $id = new \User\Services\Id();
        $id->setId((int)$request->header('X-user-id'));
        try {
            /** @var $user \User\Services\User */
            list($user, $status) = $client->getUserById($id)->wait();
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
