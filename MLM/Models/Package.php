<?php

namespace MLM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Packages\Models\Package
 *
 * @property int $id
 * @property string $name
 * @property string $short_name
 * @property int|null $validity_in_days
 * @property float $price
 * @property int|null $roi_percentage
 * @property int|null $direct_percentage
 * @property int|null $binary_percentage
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Package newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Package newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Package query()
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereBinaryPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereDirectPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereRoiPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereShortName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereValidityInDays($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\MLM\Models\PackagesIndirectCommission[] $packageIndirectCommission
 * @property-read int|null $package_indirect_commission_count
 * @property int $order_id
 * @property int $user_id
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Package whereUserId($value)
 */
class Package extends Model
{
    use HasFactory;
    protected $guarded = [];


    public function packageIndirectCommission()
    {
        return $this->hasMany(PackagesIndirectCommission::class);
    }
}
