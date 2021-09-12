<?php

namespace User\Http\Requests;

use Illuminate\Validation\Rule;
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
            'default_binary_position' => [
                'required',
                Rule::in([$LEFT, $RIGHT]),
            ],
        ];
    }
}
