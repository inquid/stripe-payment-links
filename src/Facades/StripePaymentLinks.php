<?php

namespace Inquid\StripePaymentLinks\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Inquid\StripePaymentLinks\StripePaymentLinks
 */
class StripePaymentLinks extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Inquid\StripePaymentLinks\StripePaymentLinks::class;
    }
}
