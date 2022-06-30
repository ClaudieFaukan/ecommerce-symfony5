<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function homepage(EntityManagerInterface $em)
    {
        $productRepository = $em->getRepository(Product::class);
        $products = $productRepository->find(3);
        dd($products);

        return $this->render('home.html.twig');
    }
}
