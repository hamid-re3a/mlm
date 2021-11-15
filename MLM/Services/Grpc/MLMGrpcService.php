<?php


namespace MLM\Services\Grpc;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mix\Grpc;
use Mix\Grpc\Context;
use MLM\Models\OrderedPackage;
use MLM\Models\Tree;
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
        $acknowledge = new Acknowledge();
        try {
            DB::beginTransaction();
            Log::info("simulate order");
            Log::info($request->getId());
            /** @var  $package_ordered OrderedPackage */
            Log::info('updateOrderAndPackage');
            $package_ordered = app(OrderedPackageService::class)->updateOrderAndPackage($request);
            Log::info('Done updateOrderAndPackage');

            Log::info('Check is_commission_resolved_at');
            if (is_null($package_ordered->is_commission_resolved_at)) {
                Log::info('NULL is_commission_resolved_at');

                list($status, $message) = (new OrderResolver($request))->simulateValidation();
                $acknowledge->setStatus($status);
                $acknowledge->setMessage($message);
                $acknowledge->setCreatedAt($request->getIsCommissionResolvedAt());

            } else {
                Log::info('Not null is_commission_resolved_at');

                $acknowledge->setStatus(true);
                $acknowledge->setMessage('already processed');
                $acknowledge->setCreatedAt($package_ordered->is_commission_resolved_at);

            }

            DB::rollBack();
            return $acknowledge;
        } catch (\Throwable $exception) {
            DB::rollBack();
            Log::error('MLMGrpcService@simulateOrder => ' . $exception->getMessage());
            $acknowledge->setMessage(trans('mlm.responses.something-went-wrong'));
            $acknowledge->setStatus(FALSE);
            return $acknowledge;
        }
    }

    /**
     * @inheritDoc
     */
    public function submitOrder(Context $context, OrderGrpc\Order $request): Acknowledge
    {
        Log::info("submit order");
        Log::info($request->getId());
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

    /**
     * @inheritDoc
     */
    public function getUserRank(Context $context, UserGrpc\User $request): Rank
    {
        if ($request->getId()) {
            try {
                $user = $this->user_service->findByIdOrFail($request->getId());
                $rank = $user->rank_model;

                if (!is_null($rank)) {
                    $rank_grpc = $rank->getRankService();
                    $ordered_package = $user->biggestActivePackage();
                    if (!is_null($ordered_package)) {
                        if ($ordered_package->isCompanyPackage())
                            $rank_grpc->setWithdrawalLimit((int)-1);
                        if ($ordered_package->isSpecialPackage())
                            if ($user->directSellAmount() <= $ordered_package->price)
                                $rank_grpc->setWithdrawalLimit((int)0);

                        return $rank_grpc;
                    }
                }
            } catch (\Exception $exception) {

            }
        }

        return new Rank;
    }


    /**
     * @inheritDoc
     */
    public function isUserInSecondUserDescendant(Context $context, UserDescendantCheck $request): Acknowledge
    {
        $acknowledge = new Acknowledge();
        $tree = Tree::query()->where('user_id',$request->getUserIndexId())->first();
        if(!is_null($tree) && !is_null(Tree::descendantsAndSelf($tree->id)->where('user_id',$request->getUserToShowId())->first())){
            $acknowledge->setStatus(true);
            return $acknowledge;
        }
        $acknowledge->setStatus(false);
        return $acknowledge;
    }
}
