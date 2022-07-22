<?php

namespace App\Controller;

use App\Cart\CartService;
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
     * @Route("/cart/add/{id}", name="cart_add", requirements={"id"="\d+"})
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
     * @Route("/cart", name="cart_show")
     */
    public function show(): Response
    {
        $detailCart = $this->cartService->getDetailedItems();
        $total = $this->cartService->getTotal();

        return $this->render("cart/index.html.twig", [
            'items' => $detailCart,
            'total' => $total
        ]);
    }

    /**
     * @Route("/cart/delete/{id}", name="cart_delete", requirements={"id":"\d+"})
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
     * @Route("/cart/decrement/{id}", name="cart_decrement", requirements={"id":"\d+"})
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
