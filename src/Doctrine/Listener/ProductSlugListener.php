<?php

namespace App\Doctrine\Listener;

use App\Entity\Product;
use Psr\Log\LoggerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductSlugListener
{
    protected $slugger;
    protected $logger;

    public function __construct(SluggerInterface $slugger, LoggerInterface $logger)
    {
        $this->slugger = $slugger;
        $this->logger = $logger;
    }
    /**
     * If the slug is empty, then set the slug to the lowercase version of the name.
     * 
     * @param Product entity The entity that is being persisted.
     */

    public function prePersist(Product $entity)
    {
        if (empty($entity->getSlug())) {
            // SluggerInterface
            $entity->setSlug(strtolower($this->slugger->slug($entity->getName())));
        }
        $this->logger->info("appeler est le slug est :{$entity->getSlug()} !!");
    }
}
