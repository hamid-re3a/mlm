<?php

namespace MLM\Http\Controllers\Front;


use Illuminate\Support\Carbon;
use MLM\Http\Requests\BinaryTreeRequest;
use MLM\Http\Requests\ReferralTreeRequest;
use MLM\Http\Resources\Tree\BinaryTreeResource;
use MLM\Http\Resources\Tree\ReferralTreeResource;
use MLM\Models\ReferralTree;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use MLM\Models\Tree;

class TreeController extends Controller
{
    use  ValidatesRequests;


//    public function getUserReferralWithLevelTree(ReferralTreeRequest $request)
//    {
//
//        if (auth()->check() && !auth()->user()->hasReferralNode())
//            return api()->error();
//
//        if ($request->has('id') && request('id'))
//            $tree = ReferralTree::withDepth()->query()->where('user_id', request('id'))->firstOrFail();
//        else
//            $tree = ReferralTree::withDepth()->query()->where('user_id', auth()->user()->id)->first();
//        $depth = $tree->depth;
//
//        $users = ReferralTree::withDepth()->descendantsAndSelf($tree->id)
//            ->where('depth', $depth + 10);
//
//        $sub = clone $users;
//        $sub->where('')
//        $page = 1;
//        if ($request->has('page') && request('page'))
//            $page = request('page');
//
//
//    }

    /**
     * Get Referral Tree
     * @group
     * Public User > Display Tree
     *
     * @queryParam id integer
     * @queryParam page integer
     */
    public function getUserReferralTree(ReferralTreeRequest $request)
    {

        if (auth()->check() && !auth()->user()->hasReferralNode())
            return api()->error();

        if ($request->has('id') && request('id'))
            $tree = ReferralTree::query()->where('user_id', request('id'))->firstOrFail();
        else
            $tree = ReferralTree::query()->where('user_id', auth()->user()->id)->first();

        $page = 1;
        if ($request->has('page') && request('page'))
            $page = request('page');


        $data = [
            'children' => ReferralTreeResource::collection($tree->children()->paginate(50)),
            'id' => $tree->id,
            'created_at' => $tree->created_at->timestamp,
            'user' => $tree->user,
            'user_rank' => $tree->user->rank,
            'has_children' => $tree->children()->exists(),
            'has_more' => $tree->children()->count() > $page * 25,
            'children_count' => $tree->children()->count(),
            'page' => $page
        ];
        return api()->success('', $data);
    }

    /**
     * Get Binary Tree
     * @group
     * Public User > Display Tree
     *
     * @queryParam id integer
     */
    public function getBinaryTree(BinaryTreeRequest $request)
    {


        if (auth()->check() && !auth()->user()->hasBinaryNode())
            return api()->error();

        if ($request->has('id') && request('id'))
            $tree = Tree::query()->where('user_id', request('id'))->firstOrFail();
        else
            $tree = Tree::query()->where('user_id', auth()->user()->id)->first();


        $left_child = $tree->children()->left()->first();
        $right_child = $tree->children()->right()->first();
        $data = [
            'children' => [
                (is_null($left_child)) ? (object)[] : BinaryTreeResource::make($left_child),
                (is_null($right_child)) ? (object)[] : BinaryTreeResource::make($right_child),
            ],
            'id' => $tree->id,
            'position' => $tree->position,
            'created_at' => $tree->created_at->timestamp,
            'user' => $tree->user,
            'has_children' => $tree->children()->exists(),
            'children_count_right' => $tree->rightChildCount(),
            'children_count_left' => $tree->leftChildCount(),
            'rank' => getRank($tree->user->rank)
        ];
        return api()->success('', $data);
    }
}
