<?php

namespace MLM\Http\Controllers\Front;


use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use MLM\Http\Requests\BinaryTreeMultiRequest;
use MLM\Http\Requests\MLMInfoRequest;
use MLM\Http\Requests\ReferralTreeMultiRequest;
use MLM\Models\ReferralTree;
use MLM\Models\Tree;
use User\Models\User;

class MLMController extends Controller
{
    use  ValidatesRequests;

    /**
     * Get MLM Info
     * @group
     * Public User > User Info
     *
     * @queryParam id integer
     */
    public function getMLMInfo(MLMInfoRequest $request)
    {
        /** @var  $user User */
        if ($request->has('id') && request('id'))
            $user = User::query()->find(request('id'));
        else
            $user = auth()->user();


        if (!$user->hasBinaryNode()) {
            $info = [
                'level' => 0,
                'converted_points' => 0,
                'left_leg_points' => 0,
                'right_leg_points' => 0,

                'sponsor_user' => $user->sponsor,
                'highest_package_detail' => $user->biggestActivePackage(),
                'highest_package' => optional($user->biggestActivePackage())->package,
                'rank' => $user->rank_model
            ];
        } else {
            $binary_tree = Tree::withDepth()->where('user_id', $user->id)->first();
            $depth = $binary_tree->depth;
            $max_depth = Tree::withDepth()->descendantsAndSelf($binary_tree->id)->max('depth');
            $info = [
                'level' => $max_depth - $depth,
                'converted_points' => $binary_tree->converted_points,
                'left_leg_points' => $binary_tree->leftSideChildrenPackagePrice(),
                'right_leg_points' => $binary_tree->rightSideChildrenPackagePrice(),
                'children_count_left' => $binary_tree->leftChildCount(),


                'sponsor_user' => $user->sponsor,
                'highest_package_detail' => $user->biggestActivePackage(),
                'highest_package' => optional($user->biggestActivePackage())->package,
                'rank' => $user->rank_model
            ];
        }

        return api()->success('', $info);
    }
}
