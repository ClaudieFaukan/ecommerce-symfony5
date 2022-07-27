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
    public static function getSubscribedEvents()
    {
        return [
            'product.view' => 'getProductViewLog'
        ];
    }

    public function getProductViewLog(ProductViewEvent $productViewEvent)
    {

        $this->logger->info("mail envoyer pour le produit N°-" . $productViewEvent->getProduct()->getId());
    }
}
