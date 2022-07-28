<?php

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Repository\PurchaseRepository;
use App\Stripe\StripeService;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchasePaymentController extends AbstractController
{
    /**
     * If the purchase exists, the user is the owner of the purchase and the purchase is not paid, then
     * show the payment form
     * 
     * @Route("/purchase/pay/{id}", name="purchase_payment_form")
     * @IsGranted("ROLE_USER")
     * @param id The id of the purchase
     * @param PurchaseRepository purchaseRepository The repository for the Purchase entity.
     * @param StripeService stripeService This is the service we created earlier.
     * 
     * @return The clientSecret and the purchase object.
     */
    public function showCardForm($id, PurchaseRepository $purchaseRepository, StripeService $stripeService)
    {
        $purchase = $purchaseRepository->find($id);

        if (
            !$purchase
            || ($purchase && $purchase->getUser() !== $this->getUser())
            || ($purchase && $purchase->getStatus() === Purchase::STATUS_PAID)
        ) {
            return $this->redirectToRoute("cart_show");
        }

        $intent = $stripeService->getPaymentIntent($purchase);

        return $this->render("purchase/payment.html.twig", [
            "clientSecret" => $intent->client_secret,
            "purchase" => $purchase,
            "stripePublicKey" => $stripeService->getPublicKey()
        ]);
    }
}
