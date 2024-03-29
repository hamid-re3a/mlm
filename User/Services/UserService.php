<?php


namespace User\Services;


use Spatie\Permission\Models\Role;
use User\Repository\UserRepository;
use User\Services\Grpc\User;

class UserService
{
    private $user_repository;

    public function __construct(UserRepository $user_repository)
    {
        $this->user_repository = $user_repository;
    }

    public function findByIdOrFail(int $id) : \User\Models\User
    {
        $user = $this->user_repository->findById($id);
        if(!is_null($user)){
            return $user;
        }
        $user_grpc = updateUserFromGrpcServer($id);

        if(!is_null($user_grpc) && $user_grpc->getId()){
            $user = $this->user_repository->findById($id);
            if(!is_null($user)){
                return $user;
            }
        }

        throw new \Exception('User not found => id ' . $id);

    }

    public function userUpdate(User $user)
    {
        $user_updated = $this->user_repository->editOrCreate($user);
        $user_updated->roles()->detach();
        $roles = explode(",", $user->getRole());
        foreach ($roles as $role) {
            $roleExist = Role::whereName($role)->first();
            if ($roleExist === null) {
                $role = Role::create(['name' => $role, 'guard_name' => 'api']);
                $user_updated->assignRole($role);
            } else {
                $user_updated->assignRole($roleExist);
            }
        }
    }


    public function editBinaryPosition($id, $position)
    {
        $this->user_repository->editBinaryPosition($id, $position);
    }


}
