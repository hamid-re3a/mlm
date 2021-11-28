<?php

namespace User\Models;

use Carbon\Carbon;
use MLM\Models\Commission;
use MLM\Models\OrderedPackage;
use MLM\Models\Rank;
use MLM\Models\ResidualBonusSetting;
use MLM\Models\ReferralTree;
use MLM\Models\Tree;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orders\Services\Grpc\Order;
use Orders\Services\Grpc\OrderPlans;
use Spatie\Permission\Traits\HasRoles;
use User\database\factories\UserFactory;

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
 * @method static \Illuminate\Database\Eloquent\Builder|User filter()
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
 * @property array|null $deactivated_commission_types
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
 * @property string|null $country
 * @property string|null $country_iso2
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBlockType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsDeactivate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsFreeze($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereMemberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSponsorId($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|OrderedPackage[] $ordered_packages
 * @property-read int|null $ordered_packages_count
 * @method static \User\database\factories\UserFactory factory(...$parameters)
 * @property-read \Illuminate\Database\Eloquent\Collection|ResidualBonusSetting[] $residualBonusSetting
 * @property-read int|null $residual_bonus_setting_count
 */
class User extends Model
{
    use HasFactory, HasRoles;

    protected $guarded = [];
    Protected $guard_name = 'api';


    protected $casts = [
        'deactivated_commission_types' => 'json'
    ];

    public function scopeFilter($query)
    {
        if (request()->has('username')) {
            $query->orWhere('username', 'LIKE', '%' . request()->get('username') . '%');
        }
        if (request()->has('first_name')) {
            $query->orWhere('first_name', 'LIKE', '%' . request()->get('first_name') . '%');
        }

        if (request()->has('rank')) {
            $ids = Rank::query()->where('rank_name', 'LIKE', '%' . request()->get('rank') . '%')->pluck('id');
            if ($ids)
                $query->orWhereIn('rank', $ids);
        }

        if (request()->has('ranks') AND is_array(request()->get('ranks'))) {
            $ids = [];
            foreach (request()->get('ranks') AS $rank)
                array_merge($ids, Rank::query()->where('rank_name', 'LIKE', '%' . $rank . '%')->pluck('id'));

            $query->orWhereIn('rank', $ids);
        }

        if (request()->has('last_name')) {
            $query->orWhere('last_name', 'LIKE', '%' . request()->get('last_name') . '%');
        }

        if (request()->has('email'))
            $query->orWhere('email', 'LIKE', '%' . request()->get('email') . '%');

        if (request()->has('member_id'))
            $query->orWhere('member_id', 'LIKE', '%' . request()->get('member_id') . '%');

        return $query;

    }


    public function getFullNameAttribute()
    {
        return ucwords(strtolower($this->first_name . ' ' . $this->last_name));
    }

    protected static function newFactory()
    {
        return UserFactory::new();
    }

    /**
     * relations
     */

    public function rank_model()
    {
        return $this->hasOne(Rank::class, 'rank', 'rank');
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

    public function sponsor()
    {
        return $this->belongsTo(User::class, 'sponsor_id', 'id');
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
        if ($this->hasBinaryNode()) {
            return $this->binaryTree;
        }
        return $this->binaryTree()->create();
    }


    /**
     * Methods
     */
    public function getGrpcMessage()
    {
        $this->fresh();
        $user = new \User\Services\Grpc\User();
        $user->setId((int)$this->attributes['id']);
        $user->setFirstName((string)$this->attributes['first_name']);
        $user->setLastName((string)$this->attributes['last_name']);
        $user->setUsername((string)$this->attributes['username']);
        $user->setEmail((string)$this->attributes['email']);
        $user->setMemberId((int)$this->attributes['member_id']);
        $user->setSponsorId((int)$this->attributes['sponsor_id']);
        $user->setBlockType((string)$this->attributes['block_type']);
        $user->setIsDeactivate((boolean)$this->attributes['is_deactivate']);
        $user->setIsFreeze((boolean)$this->attributes['is_freeze']);
        $user->setGender((string)$this->attributes['gender']);
        $user->setCountry((string)$this->attributes['country']);
        $user->setCountryIso2((string)$this->attributes['country_iso2']);

        if ($this->getRoleNames()->count()) {
            $role_name = implode(",", $this->getRoleNames()->toArray());
            $user->setRole($role_name);
        }

        return $user;
    }

    public function biggestActivePackage(): ?OrderedPackage
    {
        return $this->ordered_packages()->active()->biggest()->first();
    }

    public function biggestOrderedPackage(): ?OrderedPackage
    {
        return $this->ordered_packages()->biggest()->first();
    }

    public function hasActivePackage()
    {
        return is_null($this->ordered_packages()->active()->first()) ? false : true;
    }

    public function hasAnyValidOrder()
    {
        return $this->ordered_packages()->whereNotNull('is_commission_resolved_at')->exists();
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

        if ($oldest_package && now()->diffInDays(Carbon::make($oldest_package->created_at)) <= 30) {
            return true;
        }
        return false;
    }

    public function hasCompletedBinaryLegs(): bool
    {
        $this->refresh();
        $left_binary_children = $this->binaryTree->leftSideChildrenIds();
        $right_binary_children = $this->binaryTree->rightSideChildrenIds();

        $referral_children = $this->referralTree->childrenUserIds();

        $left_binary_sponsored_children = array_intersect($left_binary_children, $referral_children);
        $right_binary_sponsored_children = array_intersect($right_binary_children, $referral_children);

        if (self::hasLeastChildrenWithRank($left_binary_sponsored_children) && self::hasLeastChildrenWithRank($right_binary_sponsored_children))
            return true;

        return false;
    }

    public static function hasLeastChildrenWithRank(array $children, $rank = 0, $number_of_children = 1): bool
    {
        return User::query()->whereIn('id', $children)->where('rank', '>=', $rank)->count() >= $number_of_children;
    }

    public function residualBonusSetting()
    {
        return $this->hasMany(ResidualBonusSetting::class, 'rank', 'rank');
    }

    public function directSellAmount()
    {
        $referral_children = $this->referralTree->childrenUserIds();
        if (count($referral_children) == 0)
            return 0;
        return OrderedPackage::query()->whereIn('user_id', $referral_children)->whereIn('plan', [OrderPlans::ORDER_PLAN_PURCHASE, OrderPlans::ORDER_PLAN_START])->sum('price');
    }
}
