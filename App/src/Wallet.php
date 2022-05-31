<?php

namespace Ecotone\App;

use Ecotone\Modelling\Attribute\AggregateIdentifier;
use Ecotone\Modelling\Attribute\CommandHandler;
use Ecotone\Modelling\Attribute\EventSourcingAggregate;
use Ecotone\Modelling\Attribute\EventSourcingHandler;
use Ecotone\Modelling\WithAggregateVersioning;
use Ecotone\Messaging\Attribute\Asynchronous;


#[EventSourcingAggregate]
class Wallet
{
    use WithAggregateVersioning;

    #[AggregateIdentifier]
    private int $walletId;

    
    #[CommandHandler("registerWallet", endpointId: "registerWallet_endpoint")]
    public static function registerWallet(int $walletId): array
    {
        return [new WalletWasRegistered($walletId)];
    }
    #[Asynchronous("asynchronous_commands")]
    #[CommandHandler("addToWallet", endpointId: "addToWallet_endpoint")]
    public function add(int $amount): array
    {
        return [new MoneyWasAddedToWallet($this->walletId, $amount)];
    }

    #[Asynchronous("asynchronous_commands")]
    #[CommandHandler("subtractFromWallet", endpointId: "subtractFromWallet_endpoint")]
    public function subtract(int $amount): array
    {
        return [new MoneyWasSubtractedFromWallet($this->walletId, $amount)];
    }

    #[EventSourcingHandler]
    public function onWalletWasRegistered(WalletWasRegistered $event): void
    {
        $this->walletId = $event->walletId;
    }
}