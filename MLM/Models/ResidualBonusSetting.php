<?php

namespace MLM\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * MLM\Models\ResidualBonusSetting
 *
 * @property int $id
 * @property int $rank
 * @property int $level
 * @property int $percentage
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ResidualBonusSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ResidualBonusSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ResidualBonusSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|ResidualBonusSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResidualBonusSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResidualBonusSetting whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResidualBonusSetting wherePercentage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResidualBonusSetting whereRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResidualBonusSetting whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ResidualBonusSetting extends Model
{
    protected $guarded = [];

}
