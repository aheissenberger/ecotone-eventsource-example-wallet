<?php

require __DIR__ . "/vendor/autoload.php";
use Enqueue\Dbal\DbalConnectionFactory;

$application = \Ecotone\Lite\EcotoneLiteApplication::boostrap(
    [
        DbalConnectionFactory::class => new DbalConnectionFactory('mysql://mariadb:mariadb@db:3306/mariadb')
    ]
);

$walletId =2;
//$application->getCommandBus()->sendWithRouting("registerWallet", $walletId);
//$application->getCommandBus()->sendWithRouting("addToWallet", 100, metadata: ["aggregate.id" => $walletId]);
//$application->getCommandBus()->sendWithRouting("subtractFromWallet", 40, metadata: ["aggregate.id" => $walletId]);
 for ($i=0; $i < 100; $i++) { 
 $application->getCommandBus()->sendWithRouting("addToWallet", 2, metadata: ["aggregate.id" => $walletId]);
 //$application->getCommandBus()->sendWithRouting("subtractFromWallet", 2, metadata: ["aggregate.id" => $walletId]);
 }
// $walletBalance = $application->getQueryBus()->sendWithRouting("getWalletBalance", $walletId);


//echo $walletBalance;
