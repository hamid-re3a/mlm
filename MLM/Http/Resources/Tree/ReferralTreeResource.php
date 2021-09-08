<?php

namespace MLM\Http\Resources\Tree;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class ReferralTreeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'hasOrder' => $this->user->orders()->count() > 0,
            'hasChildren' => $this->children()->exists(),
            'hasMore' => false,
            'childrenCount' => $this->children()->count(),
            'page' => 1
        ];
    }
}
