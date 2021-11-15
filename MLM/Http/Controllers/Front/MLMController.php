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
     * @queryParam member_id integer
     * @queryParam user_id integer
     */
    public function getMLMInfo(MLMInfoRequest $request)
    {
        /** @var  $user User */
        if ($request->has('user_id') && request('user_id'))
            $user = User::query()->find(request('user_id'));
        else if ($request->has('member_id') && request('member_id'))
            $user = User::query()->where('member_id',request('member_id'))->first();
        else
            $user = auth()->user();


        if (!$user->hasBinaryNode()) {
            $info = [
                'level' => 0,
                'converted_points' => 0,
                'left_leg_points' => 0,
                'right_leg_points' => 0,

                'default_binary_position' => $user->default_binary_position,
                'sponsor_user' => $user->sponsor,
                'highest_package_detail' => $user->biggestActivePackage(),
                'highest_package' => optional($user->biggestActivePackage())->package,
                'rank' => $user->rank_model
            ];
        } else {
            $binary_tree = Tree::query()->where('user_id', $user->id)->first();
            $referral_tree = ReferralTree::withDepth()->where('user_id', $user->id)->first();
            $depth = $referral_tree->depth;
            $max_depth = ReferralTree::withDepth()->descendantsAndSelf($referral_tree->id)->max('depth');
            $info = [
                'level' => $max_depth - $depth,
                'converted_points' => $binary_tree->converted_points,
                'left_leg_points' => $binary_tree->leftSideChildrenPackagePrice(),
                'right_leg_points' => $binary_tree->rightSideChildrenPackagePrice(),
                'children_count_left' => $binary_tree->leftChildCount(),


                'default_binary_position' => $user->default_binary_position,
                'sponsor_user' => $user->sponsor,
                'highest_package_detail' => $user->biggestActivePackage(),
                'highest_package' => optional($user->biggestActivePackage())->package,
                'rank' => $user->rank_model
            ];
        }

        return api()->success('', $info);
    }
}
