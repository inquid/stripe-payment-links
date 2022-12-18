<?php

use Inquid\StripePaymentLinks\Contracts\LineItemsStripeLink;
use Inquid\StripePaymentLinks\Contracts\PriceStripeLink;
use Inquid\StripePaymentLinks\Contracts\ProductStripeLink;
use Inquid\StripePaymentLinks\StripePaymentLinks;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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

$stripePaymentLinks = null;

beforeAll(function () {
});

it('can test', function () {
    $stripePaymentLinks = new StripePaymentLinks([
        'api_key' => 'sk_test_51HJWZJIgQJMkE1LLojRjf2XwsipOZ6vG1Cyn36Tp13i35IC3fos159QtMbUf6IRCGVAvoq4SP9hLVOuLL3hStsQN00dKzPp63F',
    ]);

    $product = new Product('prod_N03eylyDkS6hd9', 'My Cool Product');
    $price = new Price('price_1MG3WxIgQJMkE1LL0DczGwJb', '10', $product);

    $paymentLink = $stripePaymentLinks
        ->setLineItems([new LineItems('price_1MG3WxIgQJMkE1LL0DczGwJb', '10')])
        ->generateStripeLink();

    expect($paymentLink->getStripeObject())->not()->toBeNull()
        ->and($paymentLink->getStripeObject()->currency)->toEqual('usd')
        ->and($paymentLink->getStripeObject()->id)->not()->toBeNull()
        ->and($paymentLink->getQrImage())->toEqual(QrCode::generate($paymentLink->getStripeObject()->url));
});
