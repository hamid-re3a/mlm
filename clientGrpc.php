<?php

use Orders\Services\Grpc\Order;
use Orders\Services\Grpc\OrderPlans;

require './vendor/autoload.php';

//$order = new Order();
//$order->setId((int)1001);
//$order->setUserId((int)2);
//$order->setIsPaidAt(now()->toString());
//$order->setPlan(OrderPlans::ORDER_PLAN_START);
////$order->setPlan(OrderPlans::ORDER_PLAN_PURCHASE);
//$order->setPackageId((int)1);
//$client = new \MLM\Services\Grpc\MLMServiceClient('staging-api-gateway.janex.org:9598', [
////$client = new \MLM\Services\Grpc\MLMServiceClient('127.0.0.1:9598', [
//    'credentials' => \Grpc\ChannelCredentials::createInsecure()
//]);
//list($reply, $status) = $client->submitOrder($order)->wait();
//print_r($status);
//var_dump($reply->getStatus());
//var_dump($reply->getMessage());
$client = new \User\Services\Grpc\UserServiceClient('staging-api-gateway.janex.org:9595', [
//$client = new \User\Services\Grpc\UserServiceClient('127.0.0.1:9595', [
    'credentials' => \Grpc\ChannelCredentials::createInsecure()
]);
$request = new \User\Services\Grpc\Id();
$request->setId((int)3);


list($reply, $status) = $client->getUserById($request)->wait();

print_r($status);
print_r($reply->getRole());
////$getdata = $reply->getGetdataarr();
//
////WalletInfo
//
//$client = new \User\Services\Grpc\UserServiceClient('127.0.0.1:9595', [
//    'credentials' => \Grpc\ChannelCredentials::createInsecure()
//]);
//$req = app(\User\Services\Grpc\WalletRequest::class);
//$req->setUserId(1);
//$req->setWalletType(\User\Services\Grpc\WalletType::BTC);
//list($reply, $status) = $client->getUserWalletInfo($req)->wait();
//
//print_r($reply->getAddress());
