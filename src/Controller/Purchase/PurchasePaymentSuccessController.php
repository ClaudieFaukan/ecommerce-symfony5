<?php

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Cart\CartService;
use App\Event\PurchaseSuccessEvent;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PurchasePaymentSuccessController extends AbstractController
{
    /**
     * The function success() is called when the user clicks on the "Pay" button. It checks if the
     * purchase exists, if it belongs to the user, and if it has already been paid. If everything is
     * ok, the purchase status is set to "paid" and the user is redirected to the purchase index page
     * 
     * @Route("/purchase/finish/{id}", name="purchase_payment_success")
     * @IsGranted("ROLE_USER")
     * 
     * @param id The id of the purchase
     * @param PurchaseRepository purchaseRepository The repository for the Purchase entity.
     * @param EntityManagerInterface em
     * @param CartService cartService The service that manages the cart
     * @param EventDispatcherInterface eventDispatcherInterface The event dispatcher.
     */
    public function success($id, PurchaseRepository $purchaseRepository, EntityManagerInterface $em, CartService $cartService, EventDispatcherInterface $eventDispatcherInterface)
    {
        $purchase = $purchaseRepository->find($id);
        if (
            !$purchase
            || ($purchase && $purchase->getUser() !== $this->getUser())
            || ($purchase && $purchase->getStatus() === Purchase::STATUS_PAID)
        ) {

            $this->addFlash("warning", "La commande n'existe pas ");
            return $this->redirectToRoute("purchase_index");
        }

        $purchase->setStatus(Purchase::STATUS_PAID);

        $em->flush();
        $cartService->empty();

        // branchement de la fonction pour envoi email lorsque validé
        $purchaseEvent = new PurchaseSuccessEvent($purchase);
        $eventDispatcherInterface->dispatch($purchaseEvent, 'purchase.success');

        $this->addFlash("success", "Commande validé !");

        return $this->redirectToRoute("purchase_index");
    }
}
