<?php

namespace MLM\Http\Resources\Tree;

use Carbon\Carbon;
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
            'created_at' => $this->created_at->timestamp,
            'user' => $this->user,
            'user_rank' => $this->user->rank,
            'has_children' => $this->children()->exists(),
            'children_count' => $this->children()->count(),
        ];
    }



}
