<?php


namespace Orders\Services\Grpc;


use Mix\Grpc\Context;
use MLM\Services\OrderResolver;
use User\Services\Grpc as UserGrpc;
use User\Services\UserService;

class OrdersGrpcService implements OrdersServiceInterface
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
        try{
            $user = $this->user_service->findByIdOrFail($request->getId());
            if($user->hasActivePackage()){
                $acknowledge->setStatus(true);
                return $acknowledge;
            }
        } catch (\Exception $exception){
            $acknowledge->setMessage($exception->getMessage());
        }

        $acknowledge->setStatus(false);
        return $acknowledge;
    }

    /**
     * @inheritDoc
     */
    public function simulateOrder(Context $context, Order $request): Acknowledge
    {
        list($status, $message) = (new OrderResolver($request))->simulateValidation();
        $acknowledge = new Acknowledge();
        $acknowledge->setStatus($status);
        $acknowledge->setMessage($message);
        return $acknowledge;
    }

    /**
     * @inheritDoc
     */
    public function submitOrder(Context $context, Order $request): Acknowledge
    {
        list($status, $message) = (new OrderResolver($request))->handle();
        $acknowledge = new Acknowledge();
        $acknowledge->setStatus($status);
        $acknowledge->setMessage($message);
        $acknowledge->setCreatedAt($request->getIsCommissionResolvedAt());
        return $acknowledge;
    }
}
