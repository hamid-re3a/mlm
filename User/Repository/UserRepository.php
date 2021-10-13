<?php


namespace User\Repository;

use User\Models\User as UserModel;
use User\Services\Grpc\User;

class UserRepository
{
    protected $entity_name = UserModel::class;
    /** @var $user_entity \User\Models\User */
    private $user_entity;

    public function __construct()
    {
        $this->user_entity = new $this->entity_name;
    }

    public function editOrCreate(User $user)
    {

        $user_find = $this->user_entity->query()->firstOrCreate(['id'=>$user->getId()]);
        $user_find->first_name = $user->getFirstName() ? $user->getFirstName() : $user_find->first_name;
        $user_find->last_name = $user->getLastName() ? $user->getLastName() : $user_find->last_name;
        $user_find->username = $user->getUsername() ? $user->getUsername() : $user_find->username;
        $user_find->email = $user->getEmail() ? $user->getEmail() : $user_find->email;
        $user_find->block_type = $user->getBlockType() ? $user->getBlockType() : $user_find->block_type;
        $user_find->sponsor_id = $user->getSponsorId() ? $user->getSponsorId() : $user_find->sponsor_id;
        $user_find->is_freeze = $user->getIsFreeze() ? $user->getIsFreeze() : $user_find->is_freeze;
        $user_find->is_deactivate = $user->getIsDeactivate() ? $user->getIsDeactivate() : $user_find->is_deactivate;
        $user_find->member_id = $user->getMemberId() ? $user->getMemberId() : $user_find->member_id;
        $user_find->gender = $user->getGender() ? $user->getGender() : $user_find->gender;
        if (!empty($user_find->getDirty())) {
            $user_find->save();
        }
        return $user_find;
    }

    public function editBinaryPosition($id, $position)
    {
        $user_entity = new $this->entity_name;
        $user = $user_entity::findOrFail($id);
        $user->default_binary_position = $position;
        $user->save();
    }

    public function findById(int $id)
    {

        $user_entity = new $this->entity_name;
        return $user_entity::find($id);
    }


}
