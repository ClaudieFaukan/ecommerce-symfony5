<?php

namespace App\Controller;

use App\Cart\CartService;
use App\Form\CartConfirmationType;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class CartController extends AbstractController
{

    protected $cartService;
    protected $productRepository;

    public function __construct(CartService $cartService, ProductRepository $productRepository)
    {
        $this->cartService = $cartService;
        $this->productRepository = $productRepository;
    }
    /**
     * It adds a product to the cart
     * 
     * @Route("/cart/add/{id}", name="cart_add", requirements={"id"="\d+"})
     * 
     * @param id The id of the product to add to the cart
     * @param Request request The request object.
     * 
     * @return Response A response object
     */
    public function add($id, Request $request): Response
    {
        $product = $this->productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException("Le produit $id n\'existe pas");
        }

        $this->cartService->add($id);
        $this->addFlash('success', "Poduit à bien été ajouter au panier");


        if ($request->query->get('returnToCart')) {
            return $this->redirectToRoute("cart_show");
        }

        return $this->redirectToRoute('product_show', [
            'slug' => $product->getSlug(),
            'category_slug' => $product->getCategory()->getSlug(),
        ]);
    }

    /** 
     * It gets the detailed items from the cart service, gets the total from the cart service, creates a
     * form, and renders the cart/index.html.twig template
     * 
     * @Route("/cart", name="cart_show")
     * @return Response A response object
     */
    public function show(): Response
    {
        $detailCart = $this->cartService->getDetailedItems();
        $total = $this->cartService->getTotal();
        $form = $this->createForm(CartConfirmationType::class);


        return $this->render("cart/index.html.twig", [
            'items' => $detailCart,
            'total' => $total,
            'confirmationForm' => $form->createView()
        ]);
    }

    /**
     * It deletes a product from the cart
     * 
     * @Route("/cart/delete/{id}", name="cart_delete", requirements={"id":"\d+"})
     * @param id The id of the product to remove from the cart
     * 
     * @return The product is being returned to the cart.
     */
    public function delete($id)
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException("Le produit n'existe pas et ne peut pas être supprimé");
        }

        $this->cartService->remove($id);
        $this->addFlash("success", "Produit supprimé du panier");

        return $this->redirectToRoute('cart_show');
    }


    /**
     * It checks if the product exists, then it calls the decrement method of the cart service, then it
     * adds a flash message and redirects to the cart page
     * 
     * @Route("/cart/decrement/{id}", name="cart_decrement", requirements={"id":"\d+"})
     * @param id The id of the product to remove from the cart
     * @return The method returns a redirect to the route cart_show.
     */
    public function decrement($id)
    {
        if (!$this->productRepository->find($id)) {
            throw $this->createNotFoundException("Le produit n'existe pas");
        }
        $this->cartService->decrement($id);

        $this->addFlash("success", "Le produit à bien été décrémenter");

        return $this->redirectToRoute("cart_show");
    }
}
