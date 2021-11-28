<?php

namespace MLM\Http\Controllers\Admin;


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
     * Country member chart
     * @group
     * Admin User > User MLM Dashboard
     * @return JsonResponse
     */
    public function countryMembersChart()
    {
        /** @var  $user User */
        $user = User::query()->first();
        $referral_tree = $user->referralTree;

        $users_table = with(new User)->getTable();

        $children_count_with_countries =
            User::query()
                ->selectRaw("count({$users_table}.id) AS total,country,country_iso2")
                ->whereHas('referralTree', function ($subQuery) use ($referral_tree) {
                    /**@var $subQuery Builder */
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
     * Admin User > User MLM Dashboard
     * @return JsonResponse
     */
    public function salesDistributionChart()
    {
        /**@var $ordered_package_repository OrderedPackageRepository*/
        $ordered_package_repository = app(OrderedPackageRepository::class);
        return api()->success(null,$ordered_package_repository->getDistributionChart(User::query()->first()->id));
    }

}
