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
            'rank' => $this->user->rank_model,
            'sponsor_user' => $this->user->sponsor,
            'parent_user' => optional($this->parent)->user,
            'avatar' => $this->getAvatar($this),
            'country' => $this->user->country,
            'country_iso2' => $this->user->country_iso2,
            'highest_package_detail' => $this->user->biggestActivePackage(),
            'highest_package' => optional($this->user->biggestActivePackage())->package,
            'has_children' => $this->children()->exists(),
            'children_count' => $this->children()->count(),
            'has_more'=> false,
            'page' => 0
        ];
    }



}
