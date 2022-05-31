<?php
require __DIR__ . "/vendor/autoload.php";
use Enqueue\Dbal\DbalConnectionFactory;
use Ecotone\Messaging\Endpoint\ExecutionPollingMetadata;
use Hhxsv5\SSE\Event;
use Hhxsv5\SSE\SSE;
use Hhxsv5\SSE\StopSSEException;

// header("Access-Control-Allow-Origin: *");
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('X-Accel-Buffering: no'); // Nginx: unbuffered responses suitable for Comet and HTTP streaming applications


$application = \Ecotone\Lite\EcotoneLiteApplication::boostrap(
    [
        DbalConnectionFactory::class => new DbalConnectionFactory('mysql://mariadb:mariadb@db:3306/mariadb')
    ]
);

$walletId =filter_input(INPUT_GET, 'wallet_id', FILTER_SANITIZE_NUMBER_INT) ?? 1;
$oldBalance=null;
$walletBalance = $application->getQueryBus()->sendWithRouting("getWalletBalance", $walletId);


$callback = function () use ($application,$walletId,&$oldBalance) {
    if ($oldBalance===null) {
        $application->run("asynchronous_serverevents",ExecutionPollingMetadata::createWithDefaults()
    ->withHandledMessageLimit(1));
    }
    $walletBalance = $application->getQueryBus()->sendWithRouting("getWalletBalance", $walletId);
    if ($walletBalance!==$oldBalance) {
        $oldBalance=$walletBalance;
        return json_encode(compact('walletBalance'));
    } else {
        return false;
    }
    
};

(new SSE(new Event($callback, 'walletBalance')))->start();
