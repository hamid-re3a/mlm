<?php

namespace User\Convert\Models;

use Illuminate\Database\Eloquent\Model;

class Individual extends Model
{
    protected $guarded = [];

    protected $table = '2297_ft_individual';
    protected $connection = 'conversion_mysql';

    public function detail()
    {
        return $this->hasOne(IndividualDetail::class,'user_detail_refid');
    }
}
