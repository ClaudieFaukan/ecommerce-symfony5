<?php

namespace App\Purchase;

use App\Cart\CartService;
use DateTime;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class PurchasePersister
{
    protected $security;
    protected $cartService;
    protected $em;

    public function __construct(Security $security, CartService $cartService, EntityManagerInterface $em)
    {
        $this->security = $security;
        $this->cartService = $cartService;
        $this->em = $em;
    }
    /**
     * It takes a purchase object, sets the user, persists the purchase, loops through the cart items,
     * creates a purchase item, sets the purchase, product, product name, quantity, total, and product
     * price, persists the purchase item, and then flushes the entity manager.
     * 
     * @param Purchase purchase The purchase object
     */

    public function storePurchase(Purchase $purchase)
    {

        $purchase->setUser($this->security->getUser());


        $this->em->persist($purchase);

        foreach ($this->cartService->getDetailedItems() as $cartItem) {

            $purchaseItem = new PurchaseItem;

            $purchaseItem->setPurchase($purchase)
                ->setProduct($cartItem->product)
                ->setProductName($cartItem->product->getName())
                ->setQuantity($cartItem->qty)
                ->setTotal($cartItem->getTotal())
                ->setProductPrice($cartItem->product->getPrice());

            $this->em->persist($purchaseItem);
        }

        $this->em->flush();
    }
}
