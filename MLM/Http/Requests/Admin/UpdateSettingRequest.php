<?php

namespace MLM\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use MLM\Models\Setting;

class UpdateSettingRequest extends FormRequest
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
            'name' => 'required|string|exists:mlm_settings,name',
            'value' => $this->valueValidation(),
            'title' => 'nullable|string',
            'description' => 'nullable|string'
        ];
    }

    public function messages()
    {
        return [
            'name.exists' => trans('mlm.responses.settings.key-doesnt-exists',['key' => $this->get('name')])
        ];
    }

    private function valueValidation()
    {
        if ($this->has('name')) {
            $settings = Setting::all();
            switch ($this->get('name')) {
                case 'IS_UNDER_MAINTENANCE' :
                    return 'required|boolean';
                    break;
                default:
                    return 'required';
            }
        }

        return 'required';
    }

}
