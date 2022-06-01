<?php

require __DIR__ . "/vendor/autoload.php";

use Enqueue\Dbal\DbalConnectionFactory;

$application = \Ecotone\Lite\EcotoneLiteApplication::boostrap(
    [
        DbalConnectionFactory::class => new DbalConnectionFactory('mysql://mariadb:mariadb@db:3306/mariadb')
    ]
);

for ($i = 1; $i <= 3; $i++) {

    $walletId = $i;
    $application->getCommandBus()->sendWithRouting("registerWallet", $walletId);
}
