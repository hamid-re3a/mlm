<?php


namespace MLM\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PackageRoiIndexRequest extends FormRequest
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
            'from_date' => 'sometimes|date_format:Y-m-d|after:2021-01-01',
            'to_date' => 'sometimes|date_format:Y-m-d|after:2021-01-01',

        ];
    }
}

