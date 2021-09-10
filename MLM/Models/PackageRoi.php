<?php

namespace MLM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



/**
 * MLM\Models\PackageRoi
 *
 * @property int $id
 * @property int $package_id
 * @property int|null $roi_percentage
 * @property string $due_date
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|PackageRoi newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PackageRoi newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PackageRoi query()
 * @method static \Illuminate\Database\Eloquent\Builder|PackageRoi whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageRoi whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageRoi whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageRoi whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageRoi wherePackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageRoi whereRoiPercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PackageRoi whereUpdatedAt($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|PackageRoi today()
 */
class PackageRoi extends Model
{
    use HasFactory;
    protected $guarded = [];


    public function scopeToday($query)
    {
        return $query->whereDate('due_date', now()->toDate());
    }

}
