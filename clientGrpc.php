<?php
require './vendor/autoload.php';
//$user = new \User\Services\Grpc\User;
//$user->setId((int)1);
//$client = new \MLM\Services\Grpc\MLMServiceClient('staging-api-gateway.janex.org:9598', [
//    'credentials' => \Grpc\ChannelCredentials::createInsecure()
//]);
//list($reply, $status) = $client->hasValidPackage($user)->wait();
//print_r($status);
$client = new \User\Services\Grpc\UserServiceClient('staging-api-gateway.janex.org:9595', [
//$client = new \User\Services\Grpc\UserServiceClient('127.0.0.1:9595', [
    'credentials' => \Grpc\ChannelCredentials::createInsecure()
]);
$request = new \User\Services\Grpc\Id();
$request->setId((int)3);


list($reply, $status) = $client->getUserById($request)->wait();

print_r($status);
print_r($reply->getFirstName());
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
