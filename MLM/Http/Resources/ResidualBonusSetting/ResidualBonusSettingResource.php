<?php


namespace MLM\Http\Resources\ResidualBonusSetting;


use Illuminate\Http\Resources\Json\JsonResource;
use MLM\Http\Resources\Rank\RankResource;

class ResidualBonusSettingResource extends JsonResource
{

    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            "level" => $this->level,
            "percentage" => $this->percentage,
            'rankCollection' => new  RankResource($this->whenLoaded('rankCollection'))
        ];
    }

}
