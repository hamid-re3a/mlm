<?php


namespace MLM\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PackageRoiUpdateRequest extends FormRequest
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
            'roi_percentage' => 'required|numeric',
            'due_date' => 'required',
            'package_id'  => [
                'required',
                'int',
                Rule::unique('package_rois')->where(function ($query) use ($request) {
                    return $query
                        ->where('package_id',isset($request['package_id'])?$request['package_id']:null)
                        ->where('due_date',isset($request['due_date'])?$request['due_date']:null);
                })
                    ->ignore(isset($request['package_id'])?$request['package_id']:null,'package_id')
                    ->where(function ($query) use ($request){
                    $query ->where('package_id',isset($request['package_id'])?$request['package_id']:null)
                        ->where('due_date',isset($request['due_date'])?$request['due_date']:null);})
            ],

        ];
    }
}

