<?php

namespace Inquid\StripePaymentLinks\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Inquid\StripePaymentLinks\StripePaymentLinksServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Stripe\Stripe;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Inquid\\StripePaymentLinks\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            StripePaymentLinksServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_stripe-payment-links_table.php.stub';
        $migration->up();
        */
    }
}
