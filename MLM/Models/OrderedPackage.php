<?php

namespace MLM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MLM\database\factories\OrderedPackageFactory;
use MLM\database\factories\PackageFactory;
use Orders\Services\Grpc\OrderPlans;
use User\Models\User;

/**
 * MLM\Models\OrderedPackage
 *
 * @property int $id
 * @property int $order_id
 * @property int|null $user_id
 * @property int|null $package_id
 * @property string|null $is_paid_at
 * @property string|null $is_resolved_at
 * @property string|null $is_commission_resolved_at
 * @property int|null $plan
 * @property int|null $validity_in_days
 * @property float|null $price
 * @property int|null $direct_percentage
 * @property int|null $binary_percentage
 * @property string|null $expires_at
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\MLM\Models\Commission[] $commissions
 * @property-read int|null $commissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\MLM\Models\OrderedPackagesIndirectCommission[] $indirectCommission
 * @property-read int|null $indirect_commission_count
 * @property-read \MLM\Models\Package|null $package
 * @property-read \Illuminate\Database\Eloquent\Collection|\MLM\Models\OrderedPackagesIndirectCommission[] $packageIndirectCommission
 * @property-read int|null $package_indirect_commission_count
 * @property-read User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackage active()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackage notSpecial()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackage biggest()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackage canGetRoi()
 * @method static \MLM\database\factories\OrderedPackageFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackage query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackage whereBinaryPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackage whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackage whereDirectPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackage whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackage whereIsCommissionResolvedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackage whereIsPaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackage whereIsResolvedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackage whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackage wherePackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackage wherePlan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackage wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackage whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackage whereValidityInDays($value)
 * @mixin \Eloquent
 */
class OrderedPackage extends Model
{
    use HasFactory;
    protected $guarded = [];



    protected static function newFactory()
    {
        return OrderedPackageFactory::new();
    }

    public function scopeActive($query)
    {
        return $query->whereDate("expires_at", ">", now()->toDate());
    }

    public function scopeNotSpecial($query)
    {
        return $query->whereNotIn("plan", [OrderPlans::ORDER_PLAN_COMPANY, OrderPlans::ORDER_PLAN_SPECIAL]);
    }
    public function scopeCanGetRoi($query)
    {
        return $query->where('plan', '!=', 'Special');
    }

    public function scopeBiggest($query)
    {
        return $query->orderBy('price', 'desc');
    }

    public function packageIndirectCommission()
    {
        return $this->hasMany(OrderedPackagesIndirectCommission::class);
    }


    public function commissions()
    {
        return $this->hasMany(Commission::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function package()
    {
        return $this->belongsTo(Package::class);
    }


    public function indirectCommission()
    {
        return $this->hasMany(OrderedPackagesIndirectCommission::class);
    }

    public function isSpecialPackage(): bool
    {
        return in_array($this->plan, [OrderPlans::ORDER_PLAN_COMPANY, OrderPlans::ORDER_PLAN_SPECIAL]);
    }


    public function isCompanyPackage(): bool
    {
        return in_array($this->plan, [OrderPlans::ORDER_PLAN_COMPANY]);
    }
}
