<?php


namespace MLM\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ResidualBonusSettingUpdateRequest extends FormRequest
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
            'id'=>'required|int',
            'level'=>'required|int',
            'percentage'=>'required|numeric',
            'rank'  => [
                'required',
                'int',
                Rule::unique('residual_bonus_settings')->where(function ($query) use ($request) {
                    return $query
                        ->whereRank(isset($request['rank'])?$request['rank']:null)
                        ->whereLevel(isset($request['level'])?$request['level']:null);
                })->ignore(isset($request['id'])?$request['id']:null,'id'),
            ]
        ];
    }
}
