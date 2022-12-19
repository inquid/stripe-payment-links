<?php

use Inquid\StripePaymentLinks\Contracts\LineItemsStripeLink;
use Inquid\StripePaymentLinks\Contracts\PriceStripeLink;
use Inquid\StripePaymentLinks\Contracts\ProductStripeLink;
use Inquid\StripePaymentLinks\StripePaymentLinks;
use Inquid\StripePaymentLinks\Tests\Mocks\StripeClientMock;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Stripe\StripeClient;

class Product implements ProductStripeLink
{
    protected string $stripeId;

    protected string $name;

    public function __construct(string $stripeId, string $name)
    {
        $this->stripeId = $stripeId;
        $this->name = $name;
    }

    public function getStripeId(): ?string
    {
        return $this->stripeId;
    }

    public function getName(): string
    {
        return $this->name;
    }
}

class Price implements PriceStripeLink
{
    protected string $stripeId;

    protected int $amount;

    protected Product $product;

    public function __construct(string $stripeId, int $amount, Product $product)
    {
        $this->stripeId = $stripeId;
        $this->amount = $amount;
        $this->product = $product;
    }

    public function getStripeId(): ?string
    {
        return $this->stripeId;
    }

    public function getUnitAmount(): int
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return 'usd';
    }

    public function getProduct(): ProductStripeLink
    {
        return $this->product;
    }

    public function getRecurring(): array
    {
        return [];
    }
}

class LineItems implements LineItemsStripeLink
{
    protected string $priceStripeLink;

    protected int $quantity;

    protected bool $allowPromotionCodes;

    protected array $adjustableQuantity;

    public function __construct(string $priceStripeLinkId, int $quantity, bool $allowPromotionCodes = false, array $adjustableQuantity = [])
    {
        $this->priceStripeLink = $priceStripeLinkId;
        $this->quantity = $quantity;
        $this->allowPromotionCodes = $allowPromotionCodes;
        $this->adjustableQuantity = $adjustableQuantity;
    }

    public function getPrice(): string
    {
        return $this->priceStripeLink;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getAdjustableQuantity(): array
    {
        return $this->adjustableQuantity;
    }
}

it('I can create a payment link with a price ID and a quantity', function () {
    // Create the mock HTTP client used by Stripe
    $mockClient = new StripeClient();
    $stripeClient = new StripeClientMock($mockClient);

    $stripePaymentLinks = new StripePaymentLinks([
        'api_key' => 'sk_test_1234',
    ], $stripeClient);

    $product = new Product('prod_1234567', 'My Cool Product');
    $price = new Price('price_1234567', '10', $product);

    $paymentLink = $stripePaymentLinks
        ->setLineItems([new LineItems('price_1234567', '10')])
        ->generateStripeLink();

    expect($paymentLink->getStripeObject())->not()->toBeNull()
        ->and($paymentLink->getStripeObject()->currency)->toEqual('usd')
        ->and($paymentLink->getStripeObject()->id)->toEqual('plink_1234567')
        ->and($paymentLink->getQrImage())->toEqual(QrCode::generate('https://checkout.stripe.com/test_payment_link'));
});
