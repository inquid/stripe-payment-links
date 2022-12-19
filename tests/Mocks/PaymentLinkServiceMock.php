<?php

namespace Inquid\StripePaymentLinks\Tests\Mocks;

use Stripe\PaymentLink;
use Stripe\Service\PaymentLinkService;

class PaymentLinkServiceMock extends PaymentLinkService
{
    public function create($params = null, $opts = null): PaymentLink
    {
        $paymentLink = new PaymentLink("plink_1234567");
        $paymentLink->currency = 'usd';
        $paymentLink->url = 'https://checkout.stripe.com/test_payment_link';

        return $paymentLink;
    }
}
