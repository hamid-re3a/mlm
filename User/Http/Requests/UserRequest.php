<?php

namespace User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $LEFT=\MLM\Models\Tree::LEFT;
        $RIGHT=\MLM\Models\Tree::RIGHT;
        return [
            'id' => 'required|int',
            'binary_position'=>'required|integer:'.$LEFT.','.$RIGHT
        ];
    }
}
