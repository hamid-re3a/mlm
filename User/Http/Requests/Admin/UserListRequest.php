<?php

namespace User\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UserListRequest extends FormRequest
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
            'username' => 'nullable|string',
            'fist_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'rank' => 'nullable|string',
            'ranks' => 'nullable|array',
            'ranks.*' => 'nullable|string',
            'email' => 'nullable|email:rfc,dns',
            'member_id' => 'nullable|numeric'
        ];
    }
}
