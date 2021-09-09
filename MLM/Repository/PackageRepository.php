<?php


namespace MLM\Repository;


use MLM\Models\Package;
use Orders\Services\Order;

class PackageRepository
{
    protected $entity_name = Package::class;

    public function updatePackage(Order $order)
    {
        /** @var  $package_entity Package */
        $package_entity = new $this->entity_name;
        $package_find = $package_entity->query()->firstOrCreate([
            'order_id' => $order->getId(),
            'package_id' => $order->getPackageId()
        ]);
        $package_find->update([
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
