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
     * @queryParam position string
     */
    public function getBinaryTreeMultiLevel(BinaryTreeMultiRequest $request)
    {

        $level = $request->has('level') ? (int)$request->level : 4;
        if (auth()->check() && !auth()->user()->hasBinaryNode())
            return api()->error();

        if ($request->has('id') AND !$this->isTreeNodeInBinaryUserDescendant($request->get('id'))) {
            return api()->notFound();
        }

        if ($request->has('id') && request('id'))
            $tree = Tree::with(['user', 'user.rank_model'])->where('user_id', request('id'))->firstOrFail();
        else
            $tree = Tree::with(['user', 'user.rank_model'])->where('user_id', auth()->user()->id)->first();


        $to_show_node = $tree;
        if (request('position') == 'right') {
            $node = $this->getRighty($tree);
            if ($node) {
                $to_show_node = $this->findTopThreeNode($node);
            }
        } else if (request('position') == 'left') {
            $node = $this->getLefty($tree);
            if ($node) {
                $to_show_node = $this->findTopThreeNode($node);
            }
        } else if (request('position') == 'top') {
            $to_show_node = $this->findTopThreeNode($tree);
        }
        list($users, $lefty, $righty) = $this->showBinaryTree($to_show_node, $level);
        return api()->success('', $this->binaryTreeResource($to_show_node, $users, $lefty, $righty));
    }


    private function binaryTreeResource($tree, &$array, $lefty, $righty)
    {
        $children = [];

        if (isset($array[$tree->id]) && $array[$tree->id]) {
            foreach ($array[$tree->id] as $item) {
                $children[] = $this->binaryTreeResource($item, $array, $lefty, $righty);
            }
        }

        return [
            'id' => $tree->id,
            'children' => $children,
//            'children_count' => $tree->children()->count(),
            'can_add_to_left' => (!$tree->hasLeftChild() && (auth()->user()->hasRole(USER_ROLE_SUPER_ADMIN) || $tree->id == optional($lefty)->id) || $tree->id == auth()->user()->binaryTree->id) ? true : false,
            'can_add_to_right' => (!$tree->hasRightChild() && (auth()->user()->hasRole(USER_ROLE_SUPER_ADMIN) || $tree->id == optional($righty)->id) || $tree->id == auth()->user()->binaryTree->id) ? true : false,
            'converted_points' => $tree->converted_points,
            'left_carry' => $tree->leftSideChildrenPackagePrice() - $tree->converted_points,
            'right_carry' => $tree->rightSideChildrenPackagePrice() - $tree->converted_points,
            'position' => $tree->position,
            'country' => $tree->user->country,
            'country_iso2' => $tree->user->country_iso2,
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

        $level = $request->has('level') ? (int)$request->level : 4;
        if (auth()->check() && !auth()->user()->hasReferralNode())
            return api()->error();

        if ($request->has('id') AND !$this->isTreeNodeInReferralUserDescendant($request->get('id'))) {
            return api()->notFound();
        }

        if ($request->has('id') && request('id'))
            $tree = ReferralTree::with('user')->where('user_id', request('id'))->firstOrFail();
        else
            $tree = ReferralTree::with('user')->where('user_id', auth()->user()->id)->first();



        $to_show_node = $tree;

        if (request('position') == 'top') {
            $to_show_node = $this->findTopThreeNode($tree);
        }


        $depth = $to_show_node->_dpt;

        $users = ReferralTree::with('user')
            ->where('_dpt', '>', $depth)
            ->where('_dpt', '<=', $depth + $level)
            ->limit(500)
            ->descendantsAndSelf($to_show_node->id)
            ->groupBy('parent_id');

        return api()->success('', $this->referralTreeResource($to_show_node, $users));
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
            'country' => $tree->user->country,
            'country_iso2' => $tree->user->country_iso2,
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
                . "api/gateway/default/general/user/avatar/" . $tree->user->member_id . "/file";
        else
            return null;
    }


    private function isTreeNodeInBinaryUserDescendant($to_show_user_id): bool
    {
        if (auth()->user()->hasBinaryNode())
            return !is_null(Tree::descendantsAndSelf(auth()->user()->binaryTree->id)->where('user_id', $to_show_user_id)->first());
        return false;
    }


    private function isTreeNodeInReferralUserDescendant($to_show_user_id): bool
    {
        if (auth()->user()->hasBinaryNode())
            return !is_null(ReferralTree::descendantsAndSelf(auth()->user()->referralTree->id)->where('user_id', $to_show_user_id)->first());
        return false;
    }


    private function leftyAndRighty(Tree $tree_node)
    {

        $lefty = $this->getLefty($tree_node);

        $righty = $this->getRighty($tree_node);

        return [$lefty, $righty];
    }


    private function getLefty(Tree $tree_node)
    {
        $lefty = Tree::query()
            ->where('_lft', '>', $tree_node->_lft)
            ->where('_rgt', '<', $tree_node->_rgt)
            ->where('position', 'left')
            ->whereIn('vacancy', [VACANCY_ALL, VACANCY_LEFT])
            ->orderBy('_lft', 'asc')
            ->limit(1)
            ->first();
        return $lefty;
    }


    private function getRighty(Tree $tree_node)
    {
        $righty = Tree::query()
            ->where('_lft', '>', $tree_node->_lft)
            ->where('_rgt', '<', $tree_node->_rgt)
            ->whereIn('vacancy', [VACANCY_ALL, VACANCY_RIGHT])
            ->where('position', 'right')
            ->orderBy('_rgt', 'desc')
            ->limit(1)
            ->first();
        return $righty;
    }

    private function findTopThreeNode($lefty, $level = 3)
    {
        $to_show = $lefty;
        if (!is_null($lefty->parent) && $level != 1) {
            $to_show = $lefty->parent;
            if (!is_null($to_show->parent) && $level != 2) {
                $to_show = $to_show->parent;
                if (!is_null($to_show->parent) && $level != 3) {
                    $to_show = $to_show->parent;
                    if (!is_null($to_show->parent) && $level != 4) {
                        $to_show = $to_show->parent;
                    }
                }
            }
        }
        return $to_show;
    }

    /**
     * @param $tree
     * @param int $level
     * @return array
     */
    private function showBinaryTree($tree, int $level): array
    {
        $depth = $tree->_dpt;

        $users = Tree::with(['user', 'user.rank_model'])
            ->where('_dpt', '>', $depth)
            ->where('_dpt', '<=', $depth + $level)
            ->descendantsAndSelf($tree->id)
            ->groupBy('parent_id');

//        ->where('_lft', '>', $tree->_lft)
//        ->where('_rgt', '<', $tree->_rgt)
//        ->orWhere('id',$tree->id)
        list($lefty, $righty) = $this->leftyAndRighty(auth()->user()->binaryTree);
        return array($users, $lefty, $righty);
    }
}
