<?php

namespace User\Models;

use Carbon\Carbon;
use MLM\Models\Commission;
use MLM\Models\OrderedPackage;
use MLM\Models\Rank;
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
 * @property-read \Illuminate\Database\Eloquent\Collection|Commission[] $commissions
 * @property-read int|null $commissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|OrderedPackage[] $packages
 * @property-read int|null $packages_count
 * @property int $rank
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRank($value)
 * @property-read Rank|null $rank_model
 * @property int|null $member_id
 * @property int|null $sponsor_id
 * @property int|null $is_deactivate
 * @property int|null $is_freeze
 * @property string|null $block_type
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBlockType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsDeactivate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsFreeze($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereMemberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSponsorId($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|OrderedPackage[] $ordered_packages
 * @property-read int|null $ordered_packages_count
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


    /**
     * relations
     */

    public function rank_model()
    {
        return $this->hasOne(Rank::class,'rank','rank');
    }
    public function commissions()
    {
        return $this->hasMany(Commission::class);
    }

    public function ordered_packages()
    {
        return $this->hasMany(OrderedPackage::class);
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

    /**
     * Methods
     */
    public function getUserService()
    {
            $user = new \User\Services\User();
            $user->setId((int)$this->attributes['id']);
            $user->setFirstName($this->attributes['first_name']);
            $user->setLastName($this->attributes['last_name']);
            $user->setUsername($this->attributes['username']);
            $user->setEmail($this->attributes['email']);
            return $user;
    }

    public function biggestActivePackage(): ?OrderedPackage
    {
        return $this->ordered_packages()->active()->biggest();
    }

    public function hasActivePackage()
    {
        return is_null($this->ordered_packages()->active()->first()) ? false : true;
    }


    public function eligibleForQuickStartBonus()
    {
        if ($this->hasRegisteredWithinThirtyDays() && $this->hasCompletedBinaryLegs()) {
            return true;
        }
        return false;
    }

    public function hasRegisteredWithinThirtyDays()
    {
        $oldest_package = $this->ordered_packages()->oldest()->first();

        if (now()->diffInDays(Carbon::make($oldest_package->createdAt())) <= 30) {
            return true;
        }
        return false;
    }

    public function hasCompletedBinaryLegs(): bool
    {

        $left_binary_children = $this->binaryTree->leftSideChildrenIds();
        $right_binary_children = $this->binaryTree->rightSideChildrenIds();

        $referral_children = $this->referralTree->childrenIds();

        $left_binary_sponsored_children = array_intersect($left_binary_children, $referral_children);
        $right_binary_sponsored_children = array_intersect($right_binary_children, $referral_children);

        if (self::hasAtLeastOnActiveUserWithRank($left_binary_sponsored_children) && self::hasAtLeastOnActiveUserWithRank($right_binary_sponsored_children))
            return true;

        return false;
    }

    public static function hasAtLeastOnActiveUserWithRank(array $children, $rank  = 1): bool
    {
        return User::query()->whereIn('id',$children)->where('rank','>=',$rank)->exists();
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return \User\database\factories\UserFactory::new();
    }

}
