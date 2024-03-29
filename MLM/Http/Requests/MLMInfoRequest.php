<?php

namespace MLM\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MLMInfoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id' => 'sometimes|exists:users',
            'member_id' => 'sometimes|exists:users,member_id',
        ];
    }
}
