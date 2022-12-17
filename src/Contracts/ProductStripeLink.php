<?php

declare(strict_types=1);

namespace Inquid\StripePaymentLinks\Contracts;

/**
 * Product Interface.
 */
interface ProductStripeLink
{
    /**
     * The stripe object ID
     */
    public function getStripeId(): ?string;

    public function getName(): string;
}
