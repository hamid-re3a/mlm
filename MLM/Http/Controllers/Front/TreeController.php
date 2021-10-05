<?php

namespace MLM\Http\Controllers\Front;


use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use MLM\Http\Requests\BinaryTreeMultiRequest;
use MLM\Http\Requests\BinaryTreeRequest;
use MLM\Http\Requests\ReferralTreeMultiRequest;
use MLM\Http\Requests\ReferralTreeRequest;
use MLM\Http\Resources\Tree\BinaryTreeResource;
use MLM\Http\Resources\Tree\ReferralTreeResource;
use MLM\Models\ReferralTree;
use MLM\Models\Tree;

class TreeController extends Controller
{
    use  ValidatesRequests;

    /**
     * Get Binary Tree Multi Level
     * @group
     * Public User > Display Tree
     *
     * @queryParam id integer
     * @queryParam level integer
     */
    public function getBinaryTreeMultiLevel(BinaryTreeMultiRequest  $request)
    {

        $level = $request->has('level') ? (int) $request->level : 6;
        if (auth()->check() && !auth()->user()->hasBinaryNode())
            return api()->error();

        if ($request->has('id') && request('id'))
            $tree = Tree::with(['user', 'user.rank_model'])->withDepth()->where('user_id', request('id'))->firstOrFail();
        else
            $tree = Tree::with(['user', 'user.rank_model'])->withDepth()->where('user_id', auth()->user()->id)->first();
        $depth = $tree->depth;

        $users = Tree::with(['user', 'user.rank_model'])->withDepth()->descendantsAndSelf($tree->id)
            ->where('depth', '<=', $depth + $level)->groupBy('parent_id');
        return api()->success('', $this->binaryTreeResource($tree, $users));
    }

    private function binaryTreeResource($tree, &$array)
    {
        $children = [];

        if (isset($array[$tree->id]) && $array[$tree->id]) {
            foreach ($array[$tree->id] as $item) {
                $children[] = $this->binaryTreeResource($item, $array);
            }
        }

        return [
            'id' => $tree->id,
            'children' => $children,
//            'children_count' => $tree->children()->count(),
            'position' => $tree->position,
            'created_at' => $tree->created_at->timestamp,
            'user' => $tree->user,
            'sponsor_user' => $tree->user->sponsor,
            'parent_user' => $tree->parent->user,
            'highest_package' => $tree->user->biggestActivePackage(),
            'has_children' => $tree->children()->exists(),
            'children_count_right' => $tree->rightChildCount(),
            'children_count_left' => $tree->leftChildCount(),
            'rank' => $tree->user->rank_model
        ];
    }

    /**
     * Get Referral Tree Multi Level
     * @group
     * Public User > Display Tree
     *
     * @queryParam id integer
     * @queryParam level integer
     */
    public function getReferralTreeMultiLevel(ReferralTreeMultiRequest $request)
    {

        $level = $request->has('level') ? (int) $request->level : 6;
        if (auth()->check() && !auth()->user()->hasReferralNode())
            return api()->error();

        if ($request->has('id') && request('id'))
            $tree = ReferralTree::with('user')->withDepth()->where('user_id', request('id'))->firstOrFail();
        else
            $tree = ReferralTree::with('user')->withDepth()->where('user_id', auth()->user()->id)->first();
        $depth = $tree->depth;

        $users = ReferralTree::with('user')->withDepth()->descendantsAndSelf($tree->id)
            ->where('depth', '<=', $depth + 10)->groupBy('parent_id');

        return api()->success('', $this->referralTreeResource($tree, $users));
    }

    private function referralTreeResource($tree, &$array)
    {
        $children = [];

        if (isset($array[$tree->id]) && $array[$tree->id]) {
            foreach ($array[$tree->id] as $item) {
                $children[] = $this->referralTreeResource($item, $array);
            }
        }

        return [
            'children' => $children,
            'id' => $tree->id,
            'created_at' => $tree->created_at->timestamp,
            'user' => $tree->user,
            'user_rank' => $tree->user->rank,
            'sponsor_user' => $tree->user->sponsor,
            'parent_user' => $tree->parent->user,
            'highest_package' => $tree->user->biggestActivePackage(),
            'has_children' => $tree->children()->exists(),
            'children_count' => $tree->children()->count(),
        ];
    }

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
            'children_count' => $tree->children()->count(),
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
