<?php

namespace App\EventDispatcher;

use Psr\Log\LoggerInterface;
use App\Event\ProductViewEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductViewLogSubscriber implements EventSubscriberInterface
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    /**
     * "When the event 'product.view' is triggered, call the function 'getProductViewLog'".
     * 
     * The event 'product.view' is triggered when a product is viewed.
     * 
     * The function 'getProductViewLog' is the function that will be called when the event is triggered.
     * 
     * @return An array of events and their associated methods.
     */
    public static function getSubscribedEvents()
    {
        return [
            'product.view' => 'getProductViewLog'
        ];
    }

    /**
     * It logs the product view event.
     * 
     * @param ProductViewEvent productViewEvent The event object that is passed to the listener.
     */
    public function getProductViewLog(ProductViewEvent $productViewEvent)
    {
        $this->logger->info("mail envoyer pour le produit N°-" . $productViewEvent->getProduct()->getId());
    }
}
