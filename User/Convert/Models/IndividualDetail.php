<?php

namespace User\Convert\Models;

use Illuminate\Database\Eloquent\Model;

class IndividualDetail extends Model
{
    protected $guarded = [];

    protected $table = '2297_user_details';
    protected $connection = 'conversion_mysql';
}
