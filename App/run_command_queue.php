<?php

require __DIR__ . "/vendor/autoload.php";
use Enqueue\Dbal\DbalConnectionFactory;

$application = \Ecotone\Lite\EcotoneLiteApplication::boostrap(
    [
        DbalConnectionFactory::class => new DbalConnectionFactory('mysql://mariadb:mariadb@db:3306/mariadb')
    ]
);
//$application->runConsoleCommand("ecotone:es:delete-projection", ["name" => 'currentBalance']);
//$application->runConsoleCommand("ecotone:es:initialize-projection", ["name" => 'currentBalance']);
$application->run("asynchronous_commands");