<?php

namespace MLM\Http\Controllers\Front;


use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use MLM\Http\Requests\BinaryTreeMultiRequest;
use MLM\Http\Requests\ReferralTreeMultiRequest;
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
    public function getBinaryTreeMultiLevel(BinaryTreeMultiRequest $request)
    {

        $level = $request->has('level') ? (int)$request->level : 6;
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
            'avatar' => $this->getAvatar($tree),
            'sponsor_user' => $tree->user->sponsor,
            'parent_user' => optional($tree->parent)->user,
            'highest_package_detail' => $tree->user->biggestActivePackage(),
            'highest_package' => optional($tree->user->biggestActivePackage())->package,
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

        $level = $request->has('level') ? (int)$request->level : 6;
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
            'rank' => $tree->user->rank_model,
            'sponsor_user' => $tree->user->sponsor,
            'parent_user' => optional($tree->parent)->user,
            'avatar' => $this->getAvatar($tree),
            'highest_package_detail' => $tree->user->biggestActivePackage(),
            'highest_package' => optional($tree->user->biggestActivePackage())->package,
            'has_children' => $tree->children()->exists(),
            'children_count' => $tree->children()->count(),
        ];
    }

    private function getAvatar($tree)
    {
        if ($tree->user->member_id)
            return env('API_GATEWAY_BASE_URL', "https://staging-api-gateway.janex.org/")
                . "api/gateway/default/general/user/avatar/" . $tree->user->member_id . "/image";
        else
            return null;
    }
}
