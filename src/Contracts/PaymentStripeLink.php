<?php

declare(strict_types=1);

namespace Inquid\StripePaymentLinks\Contracts;

/**
 * Payment Interface.
 */
interface PaymentStripeLink
{
    /**
     * The stripe object ID
     */
    public function getStripeId(): ?string;

    public function getCurrency(): string;

    public function getUnitAmount(): int;

    public function getProduct(): ProductStripeLink;

    public function getCustomUnitAmount(): array|null;
}
