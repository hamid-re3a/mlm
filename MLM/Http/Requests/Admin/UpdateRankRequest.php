<?php


namespace MLM\Http\Requests\Admin;


use Illuminate\Foundation\Http\FormRequest;

class UpdateRankRequest extends FormRequest
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
            'id' => 'required|integer|exists:ranks,id',
            'rank' => 'required|integer|unique:ranks,rank,' . $this->get('id'),
            'rank_name' => 'required|string|unique:ranks,rank_name,' . $this->get('id'),
            'condition_converted_in_bp' => 'nullable|numeric',
            'condition_sub_rank' => 'nullable|numeric',
            'condition_direct_or_indirect' => 'required|boolean',
            'prize_in_pf' => 'nullable|numeric',
            'prize_alternative' => 'nullable|string',
            'cap' => 'required|integer',
            'withdrawal_limit' => 'required|integer',
            'condition_number_of_left_children' => 'required|integer',
            'condition_number_of_right_children' => 'required|integer',
        ];
    }
}

