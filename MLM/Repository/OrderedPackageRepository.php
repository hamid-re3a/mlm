<?php


namespace MLM\Repository;


use MLM\Models\OrderedPackage;
use Orders\Services\Order;

class OrderedPackageRepository
{
    protected $entity_name = OrderedPackage::class;

    public function updatePackage(Order $order)
    {
        /** @var  $package_entity OrderedPackage */
        $package_entity = new $this->entity_name;
        $package_find = $package_entity->query()->firstOrCreate([
            'order_id' => $order->getId(),
        ]);
        $package_find->update([
            "plan" => $order->getPlan(),
            "is_paid_at" => $order->getIsPaidAt(),
            "is_resolved_at" => $order->getIsResolvedAt(),
            "is_commission_resolved_at" => $order->getIsCommissionResolvedAt(),
            "user_id" => $order->getUserId(),
            "name" => $order->getPackage()->getName(),
            "short_name" => $order->getPackage()->getShortName(),
            "validity_in_days" => $order->getPackage()->getValidityInDays(),
            "price" => $order->getPackage()->getPrice(),
            "roi_percentage" => $order->getPackage()->getRoiPercentage(),
            "direct_percentage" => $order->getPackage()->getDirectPercentage(),
            "binary_percentage" => $order->getPackage()->getBinaryPercentage(),
        ]);
        return $package_find;
    }
}
