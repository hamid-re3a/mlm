<?php


require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/public/index.php';

$grpc = new \Mix\Grpc\Server();
$grpc->register(\MLM\Services\Grpc\MLMGrpcService::class);

Swoole\Coroutine\run(function () use ($grpc) {
    $server = new Swoole\Coroutine\Http\Server('0.0.0.0', 9598, false);
    $server->handle('/', $grpc->handler());
    $server->set([
        'open_http2_protocol' => true,
        'http_compression' => false,
    ]);
    $server->start();
});
