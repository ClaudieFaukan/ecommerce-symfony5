<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class CategoryController extends AbstractController
{

    /**
     * It takes the slug of the category, finds the category in the database, and renders the category
     * page
     * 
     * @Route("/{slug}", name="product_category", priority=-1)
     * 
     * @param slug The slug of the category we want to display
     * @param CategoryRepository categoryRepository This is the repository for the Category entity.
     * 
     * @return The category page is being returned.
     */
    public function category($slug, CategoryRepository $categoryRepository)
    {
        $category = $categoryRepository->findOneBy(['slug' => $slug]);

        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }

        return $this->render('product/category.html.twig', [
            "category" => $category,
            "slug" => $slug,
        ]);
    }


    /**
     * We create a new category, create a form, handle the request, check if the form is submitted and
     * valid, persist the category, flush the entity manager, and redirect to the home page
     * 
     * @Route("/admin/category/create", name="category_create")
     * 
     * @param Request request The request object.
     * @param EntityManagerInterface em
     * @param SluggerInterface slugger
     * 
     * @return The formView is being returned.
     */
    public function create(Request $request, EntityManagerInterface $em, SluggerInterface $slugger)
    {
        $category = new Category;

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('home');
        }

        $formView = $form->createView();

        return $this->render('category/create.html.twig', [
            'formView' => $formView
        ]);
    }

    /**
     * We get the category from the database, we create a form, we handle the request, we flush the
     * entity manager and we redirect to the home page
     * 
     * @Route("/admin/category/{id}/edit", name="category_edit")
     * 
     * @param id the id of the category we want to edit
     * @param CategoryRepository categoryRepository the repository of the Category entity
     * @param Request request The request object.
     * @param EntityManagerInterface em
     * 
     * @return The form is being returned.
     */
    public function edit($id, CategoryRepository $categoryRepository, Request $request, EntityManagerInterface $em)
    {

        $category = $categoryRepository->find($id);

        if (!$category) {
            throw new NotFoundHttpException("Cette cat??gorie n'existe pas");
        }

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('home');
        }

        $formView = $form->createView();

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'formView' => $formView
        ]);
    }
}
