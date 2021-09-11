<?php

namespace MLM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Commission
 *
 * @property int $id
 * @property int $work_office_id
 * @property string $type
 * @property int|null $transaction_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Commission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Commission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Commission query()
 * @method static \Illuminate\Database\Eloquent\Builder|Commission type($type)
 * @method static \Illuminate\Database\Eloquent\Builder|Commission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commission whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commission whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commission whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commission whereWorkOfficeId($value)
 * @mixin \Eloquent
 * @property string|null $plan
 * @method static \Illuminate\Database\Eloquent\Builder|Commission plan($plan)
 * @method static \Illuminate\Database\Eloquent\Builder|Commission wherePlan($value)
 * @property int $user_id
 * @property int|null $package_id
 * @property float|null $amount
 * @method static \Illuminate\Database\Eloquent\Builder|Commission whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commission wherePackageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Commission whereUserId($value)
 * @property int $confirmed
 * @method static \Illuminate\Database\Eloquent\Builder|Commission whereConfirmed($value)
 * @property int|null $ordered_package_id
 * @method static \Illuminate\Database\Eloquent\Builder|Commission whereOrderedPackageId($value)
 * @property string|null $due_date
 * @method static \Illuminate\Database\Eloquent\Builder|Commission whereDueDate($value)
 */
class Commission extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function scopeType($query,$type)
    {
        return $query->where('type',$type);
    }
    public function scopePlan($query, $plan)
    {
        return $query->where('plan',$plan);
    }

}
