<?php

namespace App\Doctrine\Listener;

use App\Entity\Category;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategorySlugListener
{
    protected $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    /**
     * > If the slug is empty, set it to the lowercase version of the name
     * 
     * @param Category entity The entity that is being persisted.
     * @param LifecycleEventArgs event The event that was triggered.
     */
    public function prePersist(Category $entity, LifecycleEventArgs $event)
    {
        if (empty($entity->getSlug())) {
            // SluggerInterface
            $entity->setSlug(strtolower($this->slugger->slug($entity->getName())));
        }
    }
}
