<?php

namespace MLM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



/**
 * MLM\Models\OrderedPackagesIndirectCommission
 *
 * @property int $id
 * @property int $package_id
 * @property int $level
 * @property int $percentage
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \MLM\Models\OrderedPackage $package
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackagesIndirectCommission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackagesIndirectCommission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackagesIndirectCommission query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackagesIndirectCommission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackagesIndirectCommission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackagesIndirectCommission whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackagesIndirectCommission wherePackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackagesIndirectCommission wherePercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackagesIndirectCommission whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int $ordered_package_id
 * @method static \Illuminate\Database\Eloquent\Builder|OrderedPackagesIndirectCommission whereOrderedPackageId($value)
 */
class OrderedPackagesIndirectCommission extends Model
{
    use HasFactory;

    protected $table = 'ordered_packages_indirect_commissions';

    protected $guarded = [];

    public function package()
    {
        return $this->belongsTo(OrderedPackage::class);
    }
}
