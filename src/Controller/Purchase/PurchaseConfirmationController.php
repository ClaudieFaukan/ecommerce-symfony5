<?php

namespace App\Controller\Purchase;

use DateTime;
use App\Entity\Purchase;
use App\Cart\CartService;
use App\Entity\PurchaseItem;
use App\Form\CartConfirmationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchaseConfirmationController extends AbstractController
{
    protected $cartSercice;
    protected $em;

    public function __construct(CartService $cartSercice, EntityManagerInterface $em)
    {
        $this->cartSercice = $cartSercice;
        $this->em = $em;
    }

    /**
     * @Route("/purchase/confirm", name="purchase_confirm")
     * @IsGranted("ROLE_USER", message="Vous devez être connecté")
     */
    public function confirm(Request $request)
    {

        $form = $this->createForm(CartConfirmationType::class);

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {

            $this->addFlash("warning", "Vous devez remplir le formulaire");

            return $this->redirectToRoute("cart_show");
        }

        $user = $this->getUser();


        $cartItems = $this->cartSercice->getDetailedItems();

        if (count($cartItems) === 0) {

            $this->addFlash("warning", "Panier ne peut être vide");

            return $this->redirectToRoute('cart_show');
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

        $this->cartSercice->empty();

        $this->addFlash("success", "commande enregistrer");

        return $this->redirectToRoute("purchase_index");
    }
}
