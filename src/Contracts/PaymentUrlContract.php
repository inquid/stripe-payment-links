<?php

declare(strict_types=1);

namespace Inquid\StripePaymentLinks\Contracts;

use Stripe\PaymentLink;

/**
 * Payment Link interface.
 */
interface PaymentUrlContract
{
    /**
     * The stripe object ID
     */
    public function getStripeId(): ?string;

    public function getStripeObject(): ?PaymentLink;

    public function getQrImage();

    public function getQrUrl(): string;

    public function __toString(): string;
}
