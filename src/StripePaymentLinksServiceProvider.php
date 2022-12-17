<?php

namespace Inquid\StripePaymentLinks;

use Inquid\StripePaymentLinks\Commands\StripePaymentLinksCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class StripePaymentLinksServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('stripe-payment-links')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_stripe-payment-links_table')
            ->hasCommand(StripePaymentLinksCommand::class);
    }
}
