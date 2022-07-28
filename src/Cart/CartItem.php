<?php

namespace App\Cart;

use App\Entity\Product;

/* A CartItem is a product with a quantity. */

class CartItem
{
    public $product;
    public $qty;

    public function __construct(Product $product, int $qty)
    {
        $this->product = $product;
        $this->qty = $qty;
    }

    /**
     * &gt; It returns the product of the price of the product and the quantity of the product.
     * 
     * @return int The price of the product multiplied by the quantity.
     */
    public function getTotal(): int
    {
        return $this->product->getPrice() * $this->qty;
    }
}
