<?php

namespace Ecotone\App;

use Ecotone\EventSourcing\Attribute\Projection;
use Ecotone\Modelling\Attribute\EventHandler;
use Ecotone\Modelling\Attribute\QueryHandler;
use Ecotone\Messaging\Attribute\Asynchronous;
use Ecotone\Messaging\Store\Document\DocumentStore;
use Ecotone\EventSourcing\Attribute\ProjectionDelete;
use Ecotone\EventSourcing\Attribute\ProjectionInitialization;
use Ecotone\EventSourcing\Attribute\ProjectionReset;
use Ecotone\Modelling\CommandBus;

#[Asynchronous("asynchronous_projections")]
#[Projection(self::NAME, Wallet::class)]
class CurrentBalanceProjection
{
    const NAME = "currentBalance";
    const QUERY = "getWalletBalance";

    //private array $walletBalance = [];

    public function __construct(private DocumentStore $documentStore) {}

    #[EventHandler(endpointId: "onWalletRegistered")]
    public function onWalletRegistered(WalletWasRegistered $event, CommandBus $commandBus): void
    {
        //$this->walletBalance[$event->walletId] = 0;
        $this->documentStore->addDocument(
            self::NAME,
            $event->walletId,
            ["balance" => 0]
        );
        $commandBus->sendWithRouting("updateBalance",["walletId"=>$event->walletId,"balance"=>0]);
    }

    #[EventHandler(endpointId: "onMoneyWasAddedToWallet")]
    public function onMoneyWasAddedToWallet(MoneyWasAddedToWallet $event, CommandBus $commandBus): void
    {
        //$this->walletBalance[$event->walletId] += $event->amount;
        $data=["balance" => $this->getWalletBalance($event->walletId) + $event->amount];
        $this->documentStore->updateDocument(
            self::NAME,
            $event->walletId,
            $data
        );

        $commandBus->sendWithRouting("updateBalance",["walletId"=>$event->walletId,"balance"=>$data["balance"]]);
    }

    #[EventHandler(endpointId: "onMoneySubtractedFromWallet")]
    public function onMoneySubtractedFromWallet(MoneyWasSubtractedFromWallet $event, CommandBus $commandBus): void
    {
        //$this->walletBalance[$event->walletId] -= $event->amount;
        $data=["balance" => $this->getWalletBalance($event->walletId) - $event->amount];
        $this->documentStore->updateDocument(
            self::NAME,
            $event->walletId,
            $data
        );
        $commandBus->sendWithRouting("updateBalance",["walletId"=>$event->walletId,"balance"=>$data["balance"]]);
    }

    #[QueryHandler("getWalletBalance")]
    public function getWalletBalance(int $walletId): int
    {
        //return $this->walletBalance[$walletId];
        return $this->documentStore->getDocument(self::NAME, $walletId)["balance"];
    }

    #[ProjectionDelete]
    public function delete() : void
    {
        $this->documentStore->dropCollection(
            self::NAME
        );
    }
    #[ProjectionReset]
    public function reset() : void
    {
        $this->documentStore->dropCollection(
            self::NAME
        );
    }
}