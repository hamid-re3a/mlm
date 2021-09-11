<?php

namespace MLM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use User\Models\User;

/**
 * MLM\Models\OrderedPackage
 *
 * @property int $id
 * @property int $order_id
 * @property string|null $is_paid_at
 * @property string|null $is_resolved_at
 * @property string|null $is_commission_resolved_at
 * @property int $user_id
 * @property string $name
 * @property string $short_name
 * @property int $validity_in_days
 * @property float $price
 * @property int $roi_percentage
 * @property int $direct_percentage
 * @property int $binary_percentage
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\MLM\Models\Commission[] $commissions
 * @property-read int|null $commissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\MLM\Models\OrderedPackagesIndirectCommission[] $indirectCommission
 * @property-read int|null $indirect_commission_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\MLM\Models\OrderedPackagesIndirectCommission[] $packageIndirectCommission
 * @property-read int|null $package_indirect_commission_count
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackage active()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackage canGetRoi()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackage biggest()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackage query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackage whereBinaryPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackage whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackage whereDirectPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackage whereIsCommissionResolvedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackage whereIsPaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackage whereIsResolvedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackage whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackage whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackage wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackage whereRoiPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackage whereShortName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackage whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackage whereValidityInDays($value)
 * @mixin \Eloquent
 * @property-read User $user
 * @property string|null $plan
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackage wherePlan($value)
 */
class OrderedPackage extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function scopeActive($query){
        return $query->whereRaw("CURRENT_TIMESTAMP < DATE_ADD(is_paid_at, INTERVAL validity_in_days DAY)");
    }

    public function scopeCanGetRoi($query){
        return $query->where('plan','!=','Special');
    }

    public function scopeBiggest($query){
        return $query->orderBy('price','desc')->first();
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


    public function indirectCommission()
    {
        return $this->hasMany(OrderedPackagesIndirectCommission::class);
    }


}
