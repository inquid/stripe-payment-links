<?php

declare(strict_types=1);

namespace Inquid\StripePaymentLinks\Contracts;

/**
 * Line Items Interface.
 */
interface LineItemsStripeLink
{
    public function getPrice(): string;

    public function getQuantity(): int;

    public function getAdjustableQuantity(): array;
}
