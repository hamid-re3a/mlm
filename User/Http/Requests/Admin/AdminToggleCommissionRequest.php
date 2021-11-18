<?php

namespace User\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminToggleCommissionRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'deactivated_commission_type' => 'required|in:'.implode(',',COMMISSIONS),
            'user_id' => 'sometimes|exists:users,id',
            'member_id' => 'sometimes|exists:users,member_id',
        ];
    }
}
