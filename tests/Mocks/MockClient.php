<?php

namespace Inquid\StripePaymentLinks\Tests\Mocks;

// Mock the Stripe API HTTP Client

// Optionally extend Stripe\HttpClient\CurlClient
use Illuminate\Support\Str;
use Stripe\HttpClient\CurlClient;

class MockClient extends CurlClient
{
    public $rbody = '{}';

    public $rcode = 200;

    public $rheaders = [];

    public $url;

    public function __construct()
    {
        $this->url = 'https://checkout.stripe.com/pay/cs_test_'.Str::random(32);
    }

    public function request($method, $absUrl, $headers, $params, $hasFile)
    {
        // Handle Laravel Cashier creating/getting a customer
        if ($method == 'get' && strpos($absUrl, 'https://api.stripe.com/v1/customers/') === 0) {
            $this->rBody = $this->getCustomer(str_replace('https://api.stripe.com/v1/customers/', '', $absUrl));

            return [$this->rBody, $this->rcode, $this->rheaders];
        }

        if ($method == 'post' && $absUrl == 'https://api.stripe.com/v1/customers') {
            $this->rBody = $this->getCustomer('cus_'.Str::random(14));

            return [$this->rBody, $this->rcode, $this->rheaders];
        }

        // Handle creating a Stripe Checkout session
        if ($method == 'post' && $absUrl == 'https://api.stripe.com/v1/checkout/sessions') {
            $this->rBody = $this->getSession($this->url);

            return [$this->rBody, $this->rcode, $this->rheaders];
        }

        if ($method == 'post' && $absUrl == 'https://api.stripe.com/v1/payment_links') {
            $this->rBody = $this->getLink($this->url);

            return [$this->rBody, $this->rcode, $this->rheaders];
        }

        return [$this->rbody, $this->rcode, $this->rheaders];
    }

    protected function getLink()
    {
        return <<<'JSON'
{
  "id": "plink_1MGWByIgQJMkE1LL3dip5h91",
  "object": "payment_link",
  "active": true,
  "after_completion": {
    "hosted_confirmation": {
      "custom_message": null
    },
    "type": "hosted_confirmation"
  },
  "allow_promotion_codes": false,
  "application_fee_amount": null,
  "application_fee_percent": null,
  "automatic_tax": {
    "enabled": false
  },
  "billing_address_collection": "auto",
  "consent_collection": null,
  "currency": "usd",
  "custom_text": {
    "shipping_address": null,
    "submit": null
  },
  "customer_creation": "always",
  "livemode": false,
  "metadata": {
  },
  "on_behalf_of": null,
  "payment_intent_data": null,
  "payment_method_collection": "always",
  "payment_method_types": null,
  "phone_number_collection": {
    "enabled": false
  },
  "shipping_address_collection": null,
  "shipping_options": [
  ],
  "submit_type": "auto",
  "subscription_data": null,
  "tax_id_collection": {
    "enabled": false
  },
  "transfer_data": null,
  "url": "https://buy.stripe.com/test_eVa15NcZc8Gn9puaEJ"
}
JSON;
    }

    protected function getCustomer($id)
    {
        return <<<JSON
{
  "id": "$id",
  "object": "customer",
  "address": null,
  "balance": 0,
  "created": 1626897363,
  "currency": "usd",
  "default_source": null,
  "delinquent": false,
  "description": null,
  "discount": null,
  "email": null,
  "invoice_prefix": "61F72E0",
  "invoice_settings": {
    "custom_fields": null,
    "default_payment_method": null,
    "footer": null
  },
  "livemode": false,
  "metadata": {},
  "name": null,
  "next_invoice_sequence": 1,
  "phone": null,
  "preferred_locales": [],
  "shipping": null,
  "tax_exempt": "none"
}
JSON;
    }

    protected function getSession($url)
    {
        return <<<JSON
{
  "id": "cs_test_V9Gq09dEmaJ2p3tydHonjbPSr3eq3mfOn52UBVbppDLVEFQfOji1uZok",
  "object": "checkout.session",
  "allow_promotion_codes": null,
  "amount_subtotal": null,
  "amount_total": null,
  "automatic_tax": {
    "enabled": false,
    "status": null
  },
  "billing_address_collection": null,
  "cancel_url": "https://example.com/cancel",
  "client_reference_id": null,
  "currency": "MXN",
  "customer": null,
  "customer_details": null,
  "customer_email": null,
  "livemode": false,
  "locale": null,
  "metadata": {},
  "mode": "subscription",
  "payment_intent": "pi_1DoyrW2eZvKYlo2CHqEodB86",
  "payment_method_options": {},
  "payment_method_types": [
    "card"
  ],
  "payment_status": "unpaid",
  "setup_intent": null,
  "shipping": null,
  "shipping_address_collection": null,
  "submit_type": null,
  "subscription": null,
  "success_url": "https://example.com/success",
  "total_details": null,
  "url": "$url"
}
JSON;
    }
}
