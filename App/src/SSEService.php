<?php
namespace Ecotone\App;

use Ecotone\Modelling\Attribute\CommandHandler;
use Ecotone\Messaging\Attribute\Asynchronous;
use Ecotone\Modelling\EventBus;

#[Asynchronous("asynchronous_serverevents")]
class SSEService
{
    #[CommandHandler("updateBalance", endpointId: "updateBalance_endpoint")] 
    public function updateBalance( $data) : void {}
}