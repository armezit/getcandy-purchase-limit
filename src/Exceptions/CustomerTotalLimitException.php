<?php

namespace Armezit\GetCandy\PurchaseLimit\Exceptions;

class CustomerTotalLimitException extends PurchaseLimitException
{
    public function __construct()
    {
        parent::__construct('customer total limit exceeded');
    }
}
