<?php

namespace App\Event;

use App\Entity\Purchase;
use Symfony\Contracts\EventDispatcher\Event;

class PurchaseSuccessEvent extends Event
{

    private $purchase;

    public function __construct(Purchase $purchase)
    {
        $this->purchase = $purchase;
    }

    /**
     * This function returns the purchase object.
     * 
     * @return Purchase The purchase object.
     */
    public function getPurchase(): Purchase
    {
        return $this->purchase;
    }
}
