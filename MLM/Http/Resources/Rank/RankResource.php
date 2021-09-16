<?php


namespace MLM\Http\Resources\Rank;


use Illuminate\Http\Resources\Json\JsonResource;

class RankResource extends JsonResource
{

    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'rank' => $this->rank,
            'rank_name' => $this->rank_name,
            'condition_converted_in_bp' => $this->condition_converted_in_bp,
            'condition_sub_rank' => $this->condition_sub_rank,
            'condition_direct_or_indirect' => $this->condition_direct_or_indirect,
            'prize_in_pf' => $this->prize_in_pf,
            'prize_alternative' => $this->prize_alternative,
            'withdrawal_limit' => $this->withdrawal_limit,
            'condition_number_of_left_children' => $this->condition_number_of_left_children,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,

        ];

    }

}
