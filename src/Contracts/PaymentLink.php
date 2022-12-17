<?php

declare(strict_types=1);

namespace Inquid\StripePaymentLinks\Contracts;

/**
 * Payment Link interface.
 */
interface PaymentLink
{
    /**
     * The stripe object ID
     */
    public function getStripeId(): ?string;

    // LineItemsStripeLink[]
    public function getLineItems(): array;

    public function getSubscriptionData(): array;

    public function getAutomaticTax(): array;

    public function getBillingAddressCollection(): array;

    public function getShippingAddressCollection(): array;

    public function getShippingOptions(): array;

    public function getPaymentMethodTypes(): array;

    public function getConsentCollection(): array;

    public function afterCompletion(): array;
}
