<?php

namespace MLM\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * MLM\Models\Rank
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Rank newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Rank newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Rank query()
 * @mixin \Eloquent
 * @property int $id
 * @property string $rank
 * @property int $condition_converted_in_bp
 * @property int $condition_sub_rank
 * @property int|null $prize_in_pf
 * @property string|null $prize_alternative
 * @property int $cap
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Rank whereCap($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rank whereConditionConvertedInBp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rank whereConditionSubRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rank whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rank whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rank wherePrizeAlternative($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rank wherePrizeInPf($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rank whereRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rank whereUpdatedAt($value)
 */
class Rank extends Model
{
    protected $guarded = [];

}
