<?php

namespace App\Event;

use App\Entity\Product;
use Symfony\Contracts\EventDispatcher\Event;

class ProductViewEvent extends Event
{

    private $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * Public function getProduct(): Product
     * 
     * The function is public, it's called getProduct, it doesn't take any parameters, and it returns a
     * Product.
     * 
     * @return Product The product object.
     */
    public function getProduct(): Product
    {
        return $this->product;
    }
}
