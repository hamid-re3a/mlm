<?php

namespace MLM\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReferralTreeMultiRequest extends FormRequest
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
            'id' => 'sometimes|exists:users',
            'level' => 'sometimes|numeric|min:1|max:15',
            'position' => 'sometimes|in:top',
        ];
    }
}
