<?php

namespace App\Stripe;

use App\Entity\Purchase;

/* It's a class that allows you to create a payment intent with the amount and currency of the purchase */

class StripeService
{
    protected $secretKey;
    protected $publicKey;

    public function __construct(string $secretKey, string $publicKey)
    {
        $this->secretKey = $secretKey;
        $this->publicKey = $publicKey;
    }

    /**
     * It returns the public key.
     * 
     * @return string The public key.
     */
    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    /**
     * It creates a payment intent with the amount and currency of the purchase
     * 
     * @param Purchase purchase The purchase object that contains the total amount of the purchase.
     * 
     * @return The payment intent is being returned.
     */
    public function getPaymentIntent(Purchase $purchase)
    {
        \Stripe\Stripe::setApiKey($this->secretKey);
        //intention de paiement
        return \Stripe\PaymentIntent::create([
            'amount' => $purchase->getTotal(),
            'currency' => 'eur'

        ]);
    }
}
