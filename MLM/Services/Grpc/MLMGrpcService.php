<?php


namespace MLM\Services\Grpc;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mix\Grpc\Context;
use MLM\Models\OrderedPackage;
use MLM\Services\OrderedPackageService;
use MLM\Services\OrderResolver;
use Orders\Services\Grpc as OrderGrpc;
use User\Services\Grpc as UserGrpc;
use User\Services\UserService;

class MLMGrpcService implements MLMServiceInterface
{

    /** @var  $user_service UserService */
    private $user_service;

    public function __construct()
    {
        $this->user_service = app(UserService::class);
    }

    /**
     * @inheritDoc
     */
    public function hasValidPackage(Context $context, UserGrpc\User $request): Acknowledge
    {
        $acknowledge = new Acknowledge();
        try {
            $user = $this->user_service->findByIdOrFail($request->getId());
            if ($user->hasActivePackage()) {
                $acknowledge->setStatus(true);
                return $acknowledge;
            }
        } catch (\Exception $exception) {
            $acknowledge->setMessage($exception->getMessage());
        }

        $acknowledge->setStatus(false);
        return $acknowledge;
    }

    /**
     * @inheritDoc
     */
    public function simulateOrder(Context $context, OrderGrpc\Order $request): Acknowledge
    {
        DB::beginTransaction();
        Log::info("simulate");
        $acknowledge = new Acknowledge();
        try {

            /** @var  $package_ordered OrderedPackage */
            $package_ordered = app(OrderedPackageService::class)->updateOrderAndPackage($request);
            if (is_null($package_ordered->is_commission_resolved_at)) {
                list($status, $message) = (new OrderResolver($request))->simulateValidation();
                $acknowledge->setStatus($status);
                $acknowledge->setMessage($message);
                $acknowledge->setCreatedAt($request->getIsCommissionResolvedAt());

            } else {
                $acknowledge->setStatus(true);
                $acknowledge->setMessage('already processed');
                $acknowledge->setCreatedAt($package_ordered->is_commission_resolved_at);
            }
        } catch (\Exception $exception){

        }
        DB::rollBack();
        return $acknowledge;
    }

    /**
     * @inheritDoc
     */
    public function submitOrder(Context $context, OrderGrpc\Order $request): Acknowledge
    {
        Log::info("submit order");
        $acknowledge = new Acknowledge();
        /** @var  $package_ordered OrderedPackage */
        $package_ordered = app(OrderedPackageService::class)->updateOrderAndPackage($request);
        if (is_null($package_ordered->is_commission_resolved_at)) {
            list($status, $message) = (new OrderResolver($request))->handle();
            $acknowledge->setStatus($status);
            $acknowledge->setMessage($message);
            $acknowledge->setCreatedAt($request->getIsCommissionResolvedAt());

        } else {
            $acknowledge->setStatus(true);
            $acknowledge->setMessage('already processed');
            $acknowledge->setCreatedAt($package_ordered->is_commission_resolved_at);
        }

        return $acknowledge;
    }
}
