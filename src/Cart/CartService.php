<?php

namespace App\Cart;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/* It's a class that manages a shopping cart. */

class CartService
{
    protected $session;
    protected $productRepository;

    public function __construct(SessionInterface $session, ProductRepository $productRepository)
    {
        $this->session = $session;
        $this->productRepository = $productRepository;
    }
    /**
     * &gt; This function returns an array of the cart items.
     * 
     * @return array An array of the cart items.
     */

    protected function getCart(): array
    {
        return $this->session->get('cart', []);
    }

    /**
     * > It saves the cart to the session.
     * 
     * @param array cart The cart array
     */
    protected function saveCart(array $cart)
    {
        $this->session->set('cart', $cart);
    }

    /**
     * It adds an item to the cart.
     * 
     * @param int id The id of the product to add to the cart.
     */
    public function add(int $id): Void
    {
        $cart = $this->getCart();

        if (!array_key_exists($id, $cart)) {
            $cart[$id] = 0;
        }

        $cart[$id]++;

        $this->saveCart($cart);
    }

    /**
     * It takes the cart array, loops through it, and for each item in the cart, it creates a new CartItem
     * object, which is a class that I created. 
     * 
     * The CartItem class is a simple class that holds the product and the quantity. 
     * 
     * Here's the code for the CartItem class: 
     * @return CartItem[]
     */
    public function getDetailedItems(): array
    {

        $detailCart = [];

        foreach ($this->getCart() as $id => $qty) {

            $product = $this->productRepository->find($id);

            if (!$product) {
                continue;
            }

            $detailCart[] = new CartItem($product, $qty);
        }
        return $detailCart;
    }

    /**
     * It loops through the cart, finds the product, and adds the price to the total.
     * 
     * @return Int The total price of all the products in the cart.
     */
    public function getTotal(): Int
    {

        $total = 0;

        foreach ($this->getCart() as $id => $qty) {

            $product = $this->productRepository->find($id);

            if (!$product) {
                continue;
            }

            $total += $product->getPrice() * $qty;
        }
        return $total;
    }

    /**
     * It removes an item from the cart.
     * 
     * @param int id The id of the product to remove from the cart.
     */
    public function remove(int $id)
    {
        $cart = $this->getCart();
        unset($cart[$id]);

        $this->saveCart($cart);
    }

    /**
     * If the item exists in the cart, and the quantity is greater than 1, then decrement the quantity by
     * 1.
     * 
     * @param int id The id of the product to be added to the cart.
     * 
     * @return The cart is being returned.
     */
    public function decrement(int $id)
    {
        $cart = $this->getCart();

        if (!array_key_exists($id, $cart)) {
            return;
        }
        if ($cart[$id] === 1) {

            $this->remove($id);
            return;
        }
        $cart[$id]--;

        $this->saveCart($cart);
    }

    /**
     * It saves an empty array to the session.
     */
    public function empty()
    {
        $this->saveCart([]);
    }
}
