<?php


namespace MLM\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class PackageRoiBulkUpdateRequest extends FormRequest
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

            'package_id' => 'required|array',
            'due_date' => 'required|array',
            'roi_percentage' => 'required|numeric',
        ];
    }
}

