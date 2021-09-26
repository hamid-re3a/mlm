<?php


namespace MLM\Repository;


use Carbon\Carbon;
use MLM\Models\OrderedPackage;
use Orders\Services\Grpc\Order;
use Packages\Services\Grpc\Package;

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
            "expires_at" => Carbon::make($order->getIsPaidAt())->addDays($order->getValidityInDays())
        ]);
        return $package_find;
    }
}
