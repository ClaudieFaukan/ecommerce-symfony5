<?php

namespace App\Stripe;

use App\Stripe\StripeService;

class WebHookStripe
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    public function test()
    {
        \Stripe\Stripe::setApiKey($this->stripeService->getPublicKey());

        function print_log($val)
        {
            return file_put_contents('php://stderr', print_r($val, TRUE));
        }

        $payload = @file_get_contents('php://input');

        // For now, you only need to log the webhook payload so you can see
        // the structure.
        print_log($payload);
    }
}
