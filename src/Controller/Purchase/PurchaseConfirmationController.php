<?php

namespace App\Controller\Purchase;

use DateTime;
use App\Entity\Purchase;
use App\Cart\CartService;
use App\Entity\PurchaseItem;
use App\Form\CartConfirmationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PurchaseConfirmationController extends AbstractController
{
    protected $factory;
    protected $security;
    protected $router;
    protected $cartSercice;
    protected $em;

    public function __construct(FormFactoryInterface $factory, Security $security, RouterInterface $router, CartService $cartSercice, EntityManagerInterface $em)
    {
        $this->factory = $factory;
        $this->security = $security;
        $this->router = $router;
        $this->cartSercice = $cartSercice;
        $this->em = $em;
    }

    /**
     * @Route("/purchase/confirm", name="purchase_confirm")
     */
    public function confirm(Request $request)
    {
        $form = $this->factory->create(CartConfirmationType::class);

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {

            $this->addFlash("warning", "Vous devez remplir le formulaire");

            return new RedirectResponse($this->router->generate("cart_show"));
        }

        $user = $this->security->getUser();

        if (!$user) {

            throw new AccessDeniedException("Vous devez être connecté");
        }

        $cartItems = $this->cartSercice->getDetailedItems();

        if (count($cartItems) === 0) {

            $this->addFlash("warning", "Panier ne peut être vide");

            return new RedirectResponse($this->router->generate('cart_show'));
        }
        /** @var Purchase */
        $purchase = $form->getData();

        $purchase->setUser($user)
            ->setPurchasedAt(new DateTime())
            ->setTotal($this->cartSercice->getTotal());


        $this->em->persist($purchase);

        foreach ($this->cartSercice->getDetailedItems() as $cartItem) {

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

        $this->addFlash("success", "commande enregistrer");

        return new RedirectResponse($this->router->generate("purchase_index"));
    }
}
