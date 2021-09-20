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
            'created_at' => Carbon::make($this->created_at),
            'user_rank' => $this->user->rank,
            'has_children' => $this->children()->exists(),
            'has_more' => false,
            'children_count' => $this->children()->count(),
            'page' => 1
        ];
    }



    public function getUserWorkOfficeTree(Request $request)
    {
        $work_office_rule = [
            'work_office_id' => array('required', 'exists:work_offices,id'),
        ];

        $this->validate($request, $work_office_rule);
        $work_office = WorkOffice::findOrFail(request('work_office_id'));
        $tree = $work_office->treeNode();
        if (is_null($tree)) {

            return ResponseData::error(trans('responses.work-office-is-not-in-tree'));
        }
        $left_child = $work_office->treeNode()->children()->with('workOffice')->left()->first();
        $right_child = $work_office->treeNode()->children()->with('workOffice')->right()->first();
        $data = [
            'children' => [
                (is_null($left_child)) ? (object)[] : BinaryTreeResource::make($left_child),
                (is_null($right_child)) ? (object)[] : BinaryTreeResource::make($right_child),
            ],
            'id' => $tree->id,
            'position' => $tree->position,
            'created_at' => date(verta($tree->created_at)->timezone('Asia/Tehran')),
            'workOffice' => $tree->workOffice()->first(),
            'work_office_id' => $tree->workOffice()->first()->id,
            'unit_id' => $tree->workOffice()->first()->unit_id,
            'user' => ProfileResource::make($tree->workOffice->user),
            'hasChildren' => $tree->children()->exists(),
            'childrenCountRight' => $tree->rightChildCount(),
            'childrenCountLeft' => $tree->leftChildCount(),
            'plans' => $tree->workOffice->plans
        ];
        return ResponseData::success('', $data);
    }
}
