<?php

namespace Inquid\StripePaymentLinks\Models;

use Inquid\StripePaymentLinks\Contracts\PaymentUrlContract;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Stripe\PaymentLink;

class PaymentUrl implements PaymentUrlContract
{
    protected PaymentLink $paymentLink;

    public function __construct(PaymentLink $paymentLink)
    {
        $this->paymentLink = $paymentLink;
    }

    public function getStripeId(): ?string
    {
        return $this->getStripeObject()?->id;
    }

    public function getStripeObject(): ?PaymentLink
    {
        return $this->paymentLink;
    }

    public function getQrImage()
    {
        return QrCode::generate($this->getStripeObject()?->url);
    }

    // TODO
    public function getQrUrl(): string
    {
        return '';
    }

    public function __toString(): string
    {
        return $this->getStripeObject()?->url;
    }
}
