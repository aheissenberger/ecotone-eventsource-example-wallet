<?php

namespace Ecotone\App;

class CurrentBalanceUpdated
{
    public function __construct(public readonly object $data) {}
}