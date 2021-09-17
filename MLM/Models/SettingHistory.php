<?php

namespace MLM\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * MLM\Models\SettingHistories
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property double $price
 */
class SettingHistory extends Model
{
    protected $fillable = [
        'setting_id',
        'actor_id',
        'name',
        'value',
        'title',
        'description'
    ];

    protected $casts = [
        'setting_id' => 'integer',
        'actor_id' => 'integer',
        'name' => 'string',
        'value' => 'string',
    ];

    protected $table = 'mlm_setting_histories';

}
