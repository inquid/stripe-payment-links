<?php

namespace Inquid\StripePaymentLinks;

use Stripe\StripeClient;

class StripePaymentLinks
{
    protected StripeClient $stripe;

    protected array $lineItems;

    public function __construct(array $options = [])
    {
        $this->stripe = Cashier::stripe($options);
    }

    public function setItems(array $products): self
    {
    }

    public function redirectUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function allowPromotionCodes(bool $allowPromotionCodes): self
    {
        $this->allowPromotionCodes = $allowPromotionCodes;

        return $this;
    }

    public function generateStripeLink(): StripeLink
    {
        $this->stripe->paymentLinks->create(
            [
                'line_items' => [['price' => '{{PRICE_ID}}', 'quantity' => 1]],
                'after_completion' => [
                    'type' => 'redirect',
                    'redirect' => ['url' => 'https://example.com'],
                ],
            ]
        );
    }
}
