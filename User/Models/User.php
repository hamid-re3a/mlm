<?php

namespace User\Models;

use MLM\Models\ReferralTree;
use MLM\Models\Tree;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

/**
 * User\Models\User
 *
 * @property-read Tree|null $binaryTree
 * @property-read mixed $full_name
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read ReferralTree|null $referralTree
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Role[] $roles
 * @property-read int|null $roles_count
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User role($roles, $guard = null)
 * @mixin \Eloquent
 * @property int $id
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $username
 * @property string|null $email
 * @property string $default_binary_position
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDefaultBinaryPosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUsername($value)
 */
class User extends Model
{
    use HasFactory, HasRoles;

    protected $guarded = [];
    Protected $guard_name = 'api';


    public function getFullNameAttribute()
    {
        return ucwords(strtolower($this->first_name . ' ' . $this->last_name));
    }


    public function binaryTree()
    {
        return $this->hasOne(Tree::class);
    }

    public function referralTree()
    {
        return $this->hasOne(ReferralTree::class);
    }

    /**
     * methods
     */
    public function hasReferralNode()
    {
        return (bool)$this->referralTree;
    }

    public function buildReferralTreeNode()
    {
        if ($this->hasReferralNode())
            return $this->referralTree;
        return $this->referralTree()->create();
    }

    public function hasBinaryNode()
    {
        return (bool)$this->referralTree;
    }

    public function buildBinaryTreeNode()
    {
        if ($this->hasBinaryNode())
            return $this->binaryTree;
        return $this->binaryTree()->create();
    }

}
