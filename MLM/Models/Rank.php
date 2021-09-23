<?php

namespace MLM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MLM\database\factories\RankFactory;

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
 * @property int $condition_direct_or_indirect
 * @method static \Illuminate\Database\Eloquent\Builder|Rank whereConditionDirectOrIndirect($value)
 * @property int $withdrawal_limit
 * @property int $condition_number_of_left_children
 * @property int $condition_number_of_right_children
 * @method static \Illuminate\Database\Eloquent\Builder|Rank whereConditionNumberOfLeftChildren($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rank whereConditionNumberOfRightChildren($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rank whereWithdrawalLimit($value)
 * @property string $rank_name
 * @property-read \Illuminate\Database\Eloquent\Collection|\MLM\Models\ResidualBonusSetting[] $residualBonusSettings
 * @property-read int|null $residual_bonus_settings_count
 * @method static \Illuminate\Database\Eloquent\Builder|Rank whereRankName($value)
 * @method static \MLM\database\factories\RankFactory factory(...$parameters)
 */
class Rank extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function residualBonusSettings()
    {
        return $this->hasMany(ResidualBonusSetting::class, 'rank', 'rank');
    }

    protected static function newFactory()
    {
        return RankFactory::new();
    }

    public function getRankService(): \MLM\Services\Grpc\Rank
    {
        $rank = new \MLM\Services\Grpc\Rank;
        $rank->setRank((int)$this->rank);
        $rank->setRankName($this->rank_name);
        $rank->setConditionConvertedInBp((int)$this->condition_converted_in_bp);
        $rank->setConditionSubRank((int)$this->condition_sub_rank);
        $rank->setConditionDirectOrIndirect((bool)$this->condition_direct_or_indirect);
        $rank->setPrizeInPf((int)$this->prize_in_pf);
        $rank->setPrizeAlternative($this->prize_alternative);
        $rank->setCap((int)$this->cap);
        $rank->setWithdrawalLimit((int)$this->withdrawal_limit);
        $rank->setConditionNumberOfLeftChildren((int)$this->condition_number_of_left_children);
        $rank->setConditionNumberOfRightChildren((int)$this->condition_number_of_right_children);
        return $rank;
    }

}
