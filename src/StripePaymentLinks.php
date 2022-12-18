<?php

declare(strict_types=1);

namespace Inquid\StripePaymentLinks;

use Exception;
use Inquid\StripePaymentLinks\Contracts\LineItemsStripeLink;
use Inquid\StripePaymentLinks\Contracts\PaymentUrlContract;
use Inquid\StripePaymentLinks\Models\PaymentUrl;
use Laravel\Cashier\Cashier;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

/** Wrapper class to create stripe links easily */
class StripePaymentLinks
{
    /** @var StripeClient The stripe Client object */
    protected StripeClient $stripe;

    /** @var array The stripe line items */
    protected array $lineItems;
    /** @var string The URL to redirect the customer to */
    protected ?string $url = null;
    /** @var bool $allowPromotionCodes allow promotions or not */
    protected bool $allowPromotionCodes = false;
    /** @var array An array of the stripe prices ID's if available to not re-generate them */
    protected array $pricesIds = [];
    /** @var array An array of the stripe prices ID's if available to not re-generate them */
    protected array $productsIds = [];
    /** @var bool $forceProductsCreation always create the products requesting them to the Stripe API */
    protected bool $forceProductsCreation = true;

    /** @var bool $forcePriceCreation always create the prices requesting them to the Stripe API */
    protected bool $forcePriceCreation = true;
    /** @var bool $collectTaxes collect taxes or not */
    protected bool $collectTaxes = false;
    /** @var string Collect Billing Address? */
    protected bool $billingAddressCollection = false;
    /** @var string Collect Shipping Address? */
    protected bool $shippingAddressCollection = false;
    /** @var array The rate of the shipping and other options */
    protected array $shippingOptions;
    /** @var array The information of the products or services to sell */
    protected array $items;
    /** @var string $url */
    protected string $subscriptionDescription = '';
    // The trial days of the subscription
    protected int $subscriptionTrialPeriod;
    // Collects the phone of the customer
    protected bool $collectPhone;

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->stripe = Cashier::stripe($options);
    }

    /**
     * @param array $items
     * @return $this
     */
    public function setItems(array $items): StripePaymentLinks
    {
        $this->items = $items;

        return $this;
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setRedirectUrl(string $url): StripePaymentLinks
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @param bool $allowPromotionCodes
     * @return $this
     */
    public function allowPromotionCodes(bool $allowPromotionCodes): StripePaymentLinks
    {
        $this->allowPromotionCodes = $allowPromotionCodes;

        return $this;
    }

    /**
     * @param bool $collectTaxes
     * @return $this
     */
    public function collectTaxes(bool $collectTaxes): StripePaymentLinks
    {
        $this->collectTaxes = $collectTaxes;

        return $this;
    }

    /**
     * @param int $subscriptionTrialPeriod
     * @return StripePaymentLinks
     */
    public function setSubscriptionTrialPeriod(int $subscriptionTrialPeriod): StripePaymentLinks
    {
        $this->subscriptionTrialPeriod = $subscriptionTrialPeriod;

        return $this;
    }

    /**
     * @param bool $collectPhone
     * @return StripePaymentLinks
     */
    public function setCollectPhone(bool $collectPhone): StripePaymentLinks
    {
        $this->collectPhone = $collectPhone;

        return $this;
    }

    /**
     * @param array $pricesIds
     * @return StripePaymentLinks
     */
    public function setPricesIds(array $pricesIds): StripePaymentLinks
    {
        $this->pricesIds = $pricesIds;

        return $this;
    }

    /**
     * @param array $productsIds
     * @return StripePaymentLinks
     */
    public function setProductsIds(array $productsIds): StripePaymentLinks
    {
        $this->productsIds = $productsIds;

        return $this;
    }

    /**
     * @param array $lineItems
     * @return StripePaymentLinks
     * @throws Exception
     */
    public function setLineItems(array $lineItems): StripePaymentLinks
    {
        $this->lineItems = array_map(static function (LineItemsStripeLink $lineItemsStripeLink) {

            $lineItem = [
                'price'               => $lineItemsStripeLink->getPrice(),
                'quantity'            => $lineItemsStripeLink->getQuantity(),
                'adjustable_quantity' => $lineItemsStripeLink->getAdjustableQuantity(),
            ];

            if (empty($lineItem['adjustable_quantity'])) {
                unset($lineItem['adjustable_quantity']);
            }

            return $lineItem;
        }, $lineItems);

        return $this;
    }

    /**
     * @param bool $forceProductsCreation
     * @return StripePaymentLinks
     */
    public function setForceProductsCreation(bool $forceProductsCreation): StripePaymentLinks
    {
        $this->forceProductsCreation = $forceProductsCreation;

        return $this;
    }

    /**
     * @param bool $forcePriceCreation
     * @return StripePaymentLinks
     */
    public function setForcePriceCreation(bool $forcePriceCreation): StripePaymentLinks
    {
        $this->forcePriceCreation = $forcePriceCreation;

        return $this;
    }

    /**
     * @param bool $billingAddressCollection
     * @return StripePaymentLinks
     */
    public function setBillingAddressCollection(bool $billingAddressCollection): StripePaymentLinks
    {
        $this->billingAddressCollection = $billingAddressCollection;

        return $this;
    }

    /**
     * @param bool $shippingAddressCollection
     * @return StripePaymentLinks
     */
    public function setShippingAddressCollection(bool $shippingAddressCollection): StripePaymentLinks
    {
        $this->shippingAddressCollection = $shippingAddressCollection;

        return $this;
    }

    /**
     * @param array $shippingOptions
     * @return StripePaymentLinks
     */
    public function setShippingOptions(array $shippingOptions): StripePaymentLinks
    {
        $this->shippingOptions = $shippingOptions;

        return $this;
    }

    /**
     * @param string $subscriptionDescription
     * @return StripePaymentLinks
     */
    public function setSubscriptionDescription(string $subscriptionDescription): StripePaymentLinks
    {
        $this->subscriptionDescription = $subscriptionDescription;

        return $this;
    }

    /**
     * Generates the Payment Link.
     *
     * @return \Inquid\StripePaymentLinks\Models\PaymentUrlContract
     * @throws ApiErrorException
     */
    public function generateStripeLink(): PaymentUrlContract
    {
        if ($this->url !== null) {
            $constructor['after_completion'] = [
                'type'     => 'redirect',
                'redirect' => ['url' => $this->url],
            ];
        }

        $constructor['line_items'] = $this->lineItems;

        return new PaymentUrl($this->stripe->paymentLinks->create([$constructor]));
    }
}
