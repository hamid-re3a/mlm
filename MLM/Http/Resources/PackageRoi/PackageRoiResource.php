<?php


namespace MLM\Http\Resources\PackageRoi;


use Illuminate\Http\Resources\Json\JsonResource;

class PackageRoiResource extends JsonResource
{

    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'package_id' => $this->package_id,
            'user_id' => $this->user_id,
            'roi_percentage' => $this->roi_percentage,
            'due_date' => $this->due_date,
        ];
    }

}
