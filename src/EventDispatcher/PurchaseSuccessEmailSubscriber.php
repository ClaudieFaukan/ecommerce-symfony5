<?php

namespace App\EventDispatcher;


use App\Entity\User;
use Psr\Log\LoggerInterface;
use App\Event\PurchaseSuccessEvent;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PurchaseSuccessEmailSubscriber implements EventSubscriberInterface
{
    protected $logger;
    protected $mailer;
    protected $security;

    public function __construct(LoggerInterface $logger, MailerInterface $mailer, Security $security)
    {
        $this->logger = $logger;
        $this->mailer = $mailer;
        $this->security = $security;
    }

    /**
     * "When the event 'purchase.success' is triggered, call the function 'sendSuccessEmail'".
     * 
     * The event name is arbitrary, but it's a good idea to use a name that describes the event.
     * 
     * The function name is also arbitrary, but it's a good idea to use a name that describes the function.
     * 
     * 
     * The function name is the name of a function that you will create in the same class.
     * 
     * @return An array of events and their associated methods.
     */
    public static function getSubscribedEvents()
    {
        return [
            'purchase.success' => 'sendSuccessEmail'
        ];
    }

    /**
     * It sends an email to the user who just made a purchase
     * 
     * @param PurchaseSuccessEvent purchaseSuccessEvent The event that was triggered.
     */
    public function sendSuccessEmail(PurchaseSuccessEvent $purchaseSuccessEvent)
    {
        /** @var User */
        $currentUser = $this->security->getUser();
        $purchase = $purchaseSuccessEvent->getPurchase();

        $email = new TemplatedEmail();

        $email->from("contact@mail.com")
            ->to(new Address($currentUser->getEmail(), $currentUser->getFullName()))
            ->subject("Confirmation commande N°-" . $purchase->getId())
            ->htmlTemplate("email/purchase_success.html.twig")
            ->context([
                'purchase' => $purchase,
                'user' => $currentUser
            ]);

        $this->mailer->send($email);

        $this->logger->info("succes du mail", [$purchaseSuccessEvent->getPurchase()->getId()]);
    }
}
