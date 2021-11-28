<?php

namespace MLM\Http\Controllers\Front;


use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use MLM\Http\Requests\Dashboard\DashboardRequest;
use MLM\Models\OrderedPackage;
use MLM\Models\Package;
use MLM\Models\Tree;
use MLM\Repository\OrderedPackageRepository;
use User\Models\User;

class DashboardController extends Controller
{
    use  ValidatesRequests;

    /**
     * Binary member chart
     * @group
     * Public User > User MLM Dashboard
     *
     */
    public function binaryMembersChart(DashboardRequest $request)
    {
        if (auth()->check() && !auth()->user()->hasBinaryNode())
            return api()->error();
        /** @var  $user User */
        $user = auth()->user();
        $function_left_members = function ($from_day, $to_day) use ($user) {
            if (!$user->binaryTree->hasLeftChild()) {
                return null;
            }
            return Tree::query()->select('created_at')->whereBetween('created_at', [$from_day, $to_day])->descendantsAndSelf($user->binaryTree->leftChild()->id);
        };
        $function_right_members = function ($from_day, $to_day) use ($user) {
            if (!$user->binaryTree->hasRightChild()) {
                return null;
            }
            return Tree::query()->select('created_at')->whereBetween('created_at', [$from_day, $to_day])->descendantsAndSelf($user->binaryTree->rightChild()->id);
        };
        $sub_function = function ($collection, $intervals) {
            if (is_null($collection))
                return 0;
            return $collection->whereBetween('created_at', $intervals)->count();
        };

        $final_result = [];
        $final_result['left'] = chartMaker($request->type, $function_left_members, $sub_function);
        $final_result['right'] = chartMaker($request->type, $function_right_members, $sub_function);
        return api()->success('', $final_result);
    }


    /**
     * Country member chart
     * @group
     * Public User > User MLM Dashboard
     * @return \Illuminate\Http\JsonResponse
     */
    public function countryMembersChart()
    {
        if (auth()->check() && !auth()->user()->hasBinaryNode())
            return api()->error();
        /** @var  $user User */
        $user = auth()->user();
        $referral_tree = $user->referralTree;

        $users_table = with(new User)->getTable();

        $children_count_with_countries =
            User::query()
                ->selectRaw('count(users.id) AS total,country,country_iso2')
                ->whereHas('referralTree', function ($subQuery) use ($referral_tree) {
                    /**@var $subQuery Builder*/
                    $subQuery
                        ->where('_lft', '>', $referral_tree->_lft)
                        ->where('_rgt', '<', $referral_tree->_rgt);
                })->where($users_table . '.id', '<>', $user->id)
                ->groupBy('users.country_iso2')
                ->get();

        return api()->success('', $children_count_with_countries->toArray());
    }



    /**
     * Sales distribution chart
     * @group
     * Public User > User MLM Dashboard
     * @return JsonResponse
     */
    public function salesDistributionChart()
    {
        /**@var $ordered_package_repository OrderedPackageRepository*/
        $ordered_package_repository = app(OrderedPackageRepository::class);
        return api()->success(null,$ordered_package_repository->getDistributionChart(auth()->user()->id));
    }
}
