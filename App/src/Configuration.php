<?php

namespace Ecotone\App;

use Ecotone\Dbal\Configuration\DbalConfiguration;
use Ecotone\EventSourcing\EventSourcingConfiguration;
use Ecotone\Messaging\Attribute\ServiceContext;
use Ecotone\Messaging\Attribute\ServiceActivator;
use Ecotone\EventSourcing\ProjectionRunningConfiguration;
use Ecotone\Dbal\DbalBackedMessageChannelBuilder;
use Ecotone\Messaging\Handler\Recoverability\ErrorHandlerConfiguration;
use Ecotone\Messaging\Handler\Recoverability\RetryTemplateBuilder;
use Ecotone\Messaging\Support\ErrorMessage;

class Configuration
{
    /*
    #[ServiceContext]
    public function inMemoryEventStorage()
    {
        return [
            // setting up in memory event sourcing
            EventSourcingConfiguration::createInMemory(),
            // turning off default database transactions
            DbalConfiguration::createWithDefaults()
                ->withTransactionOnCommandBus(false)
        ];
    }
*/

    #[ServiceContext]
    public function dbEventStorage()
    {
        $snapshotGivenAggregates = [Wallet::class];
        $threshold = 100;
        return [
            DbalConfiguration::createWithDefaults()
            ->withTransactionOnCommandBus(false)
            ->withDocumentStore(initializeDatabaseTable: true),
            // setting updb event sourcing
            EventSourcingConfiguration::createWithDefaults()
                /* ->withSingleStreamPersistenceStrategy() */
                 ->withSnapshots($snapshotGivenAggregates, $threshold)
           
        ];
    }

    #[ServiceContext]
    public function asyncProjection()
    {
        return DbalBackedMessageChannelBuilder::create("asynchronous_projections");
    }

    #[ServiceContext]
    public function asyncCommands()
    {
        return DbalBackedMessageChannelBuilder::create("asynchronous_commands");
    }

    #[ServiceContext]
    public function asyncServerEvents()
    {
        return DbalBackedMessageChannelBuilder::create("asynchronous_serverevents");
    }

    #[ServiceContext]
    public function errorConfiguration()
    {
        return ErrorHandlerConfiguration::createWithDeadLetterChannel(
            "errorChannel",
            RetryTemplateBuilder::exponentialBackoff(1000, 10)
                ->maxRetryAttempts(3),
            "finalErrorChannel"
        );
    }

    #[ServiceActivator("finalErrorChannel")]
    public function handle(ErrorMessage $errorMessage): void
    {
        // do something with ErrorMessage
    }

    // #[ServiceContext]
    // public function CurrentBalanceProjection()
    // {
    //   return ProjectionRunningConfiguration::createPolling("currentBalance");
    //}
}
