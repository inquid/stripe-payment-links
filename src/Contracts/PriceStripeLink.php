<?php

declare(strict_types=1);

namespace Inquid\StripePaymentLinks\Contracts;

/**
 * Price Interface.
 */
interface PriceStripeLink
{
    /**
     * The stripe object ID
     */
    public function getStripeId(): ?string;

    public function getUnitAmount(): int;

    public function getCurrency(): string;

    public function getProduct(): ProductStripeLink;

    // ['interval' => 'month']
    public function getRecurring(): array;
}
