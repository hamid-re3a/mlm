<?php


namespace MLM\Repository;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use MLM\Models\OrderedPackage;
use Orders\Services\Grpc\Order;
use Packages\Services\Grpc\IndirectCommission;
use Packages\Services\Grpc\Package;
use User\Models\User;

class OrderedPackageRepository
{
    protected $entity_name = OrderedPackage::class;

    public function updateOrderAndPackage(Order $order, Package $package)
    {
        /** @var  $package_entity OrderedPackage */
        $package_entity = new $this->entity_name;
        $package_find = $package_entity->query()->firstOrCreate([
            'order_id' => $order->getId(),
        ]);
        if (!is_null($package_find->is_commission_resolved_at))
            return $package_find;
        $package_find->update([
            "user_id" => $order->getUserId(),
            "package_id" => $package->getId(),

            "plan" => $order->getPlan(),
            "is_paid_at" => empty($order->getIsPaidAt()) ? null : Carbon::make($order->getIsPaidAt()),
            "is_resolved_at" => empty($order->getIsResolvedAt()) ? null : Carbon::make($order->getIsResolvedAt()),
            "is_commission_resolved_at" => empty($order->getIsCommissionResolvedAt()) ? null : Carbon::make($order->getIsCommissionResolvedAt()),

            "validity_in_days" => $package->getValidityInDays(),
            "price" => $package->getPrice(),
            "direct_percentage" => $package->getDirectPercentage(),
            "binary_percentage" => $package->getBinaryPercentage(),
            "expires_at" => Carbon::make($order->getIsPaidAt())->addDays($order->getValidityInDays()),
            "created_at" => Carbon::make($order->getCreatedAt()),
            "updated_at" => Carbon::make($order->getUpdatedAt()),
        ]);


        /** @var $indirect_commission IndirectCommission */
        foreach ($package->getIndirectCommission() as $indirect_commission){
            $indirect_commission_db = $package_find->packageIndirectCommission()->firstOrCreate(['level'=>$indirect_commission->getLevel()]);
            $indirect_commission_db->update(['percentage'=>$indirect_commission->getPercentage()]);
        }

        return $package_find;
    }

    public function getDistributionChart(int $user_id) : array
    {
        //TODO need refactor
        $b_package_ids = \MLM\Models\Package::query()->where('short_name', 'LIKE', 'B%')->pluck('id');
        $i_package_ids = \MLM\Models\Package::query()->where('short_name', 'LIKE', 'I%')->pluck('id');
        $a_package_ids = \MLM\Models\Package::query()->where('short_name', 'LIKE', 'A%')->pluck('id');

        $package_type_ids = [
            'B' => $b_package_ids,
            'I' => $i_package_ids,
            'A' => $a_package_ids
        ];

        /** @var  $user User */
        $user = User::query()->first();
        $referral_tree = $user->referralTree;

        $users_table = with(new User)->getTable();
        $ordered_package_table = with(new OrderedPackage())->getTable();


        $count_results = collect();

        foreach($package_type_ids AS $type => $ids) {
            $count = OrderedPackage::query()
                ->whereHas('user', function ($userSubQuery) use ($referral_tree, $user, $users_table) {
                    /**@var $userSubQuery Builder */
                    $userSubQuery->whereHas('referralTree', function ($subQuery) use ($referral_tree) {
                        /**@var $subQuery Builder */
                        $subQuery
                            ->where('_lft', '>', $referral_tree->_lft)
                            ->where('_rgt', '<', $referral_tree->_rgt);
                    })->where($users_table . '.id', '<>', $user->id);
                })->whereIn("{$ordered_package_table}.package_id",$ids)->count();
            $count_results->push([
                'name' => $type,
                'count' => $count
            ]);
        }

        return [
            'B' => $count_results->sum('count') != 0 ? $count_results->where('name','=','B')->pluck('count')['0'] * 100 / $count_results->sum('count') : 0,
            'I' => $count_results->sum('count') != 0 ?$count_results->where('name','=','I')->pluck('count')['0'] * 100 / $count_results->sum('count') : 0,
            'A' => $count_results->sum('count') != 0 ?$count_results->where('name','=','A')->pluck('count')['0'] * 100 / $count_results->sum('count') : 0,
        ];
    }
}
