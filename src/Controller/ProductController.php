<?php

namespace App\Controller;

use App\Entity\Product;
use App\Event\ProductViewEvent;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\Form\FormFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ProductController extends AbstractController
{

    /**
     * @Route("/{category_slug}/{slug}", name="product_show", priority=-1)
     */
    public function show($slug, ProductRepository $productRepository, EventDispatcherInterface $eventDispatcherInterface)
    {


        $product = $productRepository->findOneBy(['slug' => $slug]);

        if (!$product) {
            throw $this->createNotFoundException("Produit inexistant");
        }

        //Test d'ecouteur d'event sur chaque page vue
        $eventSubscriber = new ProductViewEvent($product);
        $eventDispatcherInterface->dispatch($eventSubscriber, "product.view");

        return $this->render('product/show.html.twig', [
            "product" => $product,
            "slug" => $slug,
        ]);
    }
    /**
     * @Route("/admin/product/{id}/edit", name="product_edit")
     */
    public function edit($id, ProductRepository $productRepository, Request $request, EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $product = $productRepository->find($id);

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug()
            ]);
        }

        $formView = $form->createView();

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'formView' => $formView
        ]);
    }

    /**
     * @Route("/admin/product/create", name="product_create")
     */
    public function create(Request $request, SluggerInterface $slugger, EntityManagerInterface $em)
    {
        $product = new Product;

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setSlug(strtolower($slugger->slug($product->getName())));

            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug()
            ]);
        }

        $formView = $form->createView();

        return  $this->render('product/create.html.twig', [
            'formView' => $formView
        ]);
    }
}
