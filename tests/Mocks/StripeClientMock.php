<?php

namespace Inquid\StripePaymentLinks\Tests\Mocks;

use Stripe\Service\PaymentLinkService;
use Stripe\StripeClient;

/**
 * @property PaymentLinkService $paymentLinks
 */
class StripeClientMock extends StripeClient
{
    public PaymentLinkService $paymentLinks;

    public function __construct(StripeClient $stripeClient)
    {
        $this->paymentLinks = new PaymentLinkServiceMock($stripeClient);
    }
}
