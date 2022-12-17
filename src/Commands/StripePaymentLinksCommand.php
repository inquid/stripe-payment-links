<?php

namespace Inquid\StripePaymentLinks\Commands;

use Illuminate\Console\Command;

class StripePaymentLinksCommand extends Command
{
    public $signature = 'stripe-payment-links';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
