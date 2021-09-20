<?php

namespace MLM\Http\Resources\Tree;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class BinaryTreeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'position' => $this->position,
            'created_at' => Carbon::make($this->created_at),
            'user' => $this->user,
            'has_children' => $this->children()->exists(),
            'children_count_right' => $this->rightChildCount(),
            'children_count_left' => $this->leftChildCount(),
            'rank' => getRank($this->user->rank)
        ];
    }
}
