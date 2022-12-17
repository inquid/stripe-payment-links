<?php

declare(strict_types=1);

namespace Inquid\StripePaymentLinks\Contracts;

/**
 * Line Items Interface.
 */
interface LineItemsStripeLink
{
    public function getPrice(): PriceStripeLink;

    public function getQuantity(): int;

    public function getAllowPromotionCodes(): bool;
    
    public function getAdjustableQuantity(): array;
}
