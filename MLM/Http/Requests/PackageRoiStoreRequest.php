<?php

namespace MLM\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PackageRoiStoreRequest extends FormRequest
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
        $request=$this->request->all();
        return [
            'roi_percentage' => 'required|numeric'.'|max:'.getSetting('MAX_ROI_PERCENTAGE').'|min:'.getSetting('MIN_ROI_PERCENTAGE'),
            'due_date' => 'required',
            'package_id'  => [
                'required',
                'int',
                Rule::unique('package_rois')->where(function ($query) use ($request) {
                    return $query
                        ->where('package_id',isset($request['package_id'])?$request['package_id']:null)
                        ->where('due_date',isset($request['due_date'])?$request['due_date']:null);
                }),
            ],

        ];
    }
}
