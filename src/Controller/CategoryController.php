<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use App\Repository\FilmRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{
    /**
     * @Route("/films/category/show/{slug}", name="app_films_category_show")
     */
    public function show($slug, CategoryRepository $categoryRepository, FilmRepository $filmRepository): Response
    {
        $category = $categoryRepository->findOneBy(['slug' => $slug]);

        $films = $filmRepository->findBy(['category' => $category], ['createdAt' => 'DESC']);

        return $this->render('category/show.html.twig', [
            'category' => $category,
            'films' => $films,
        ]);
    }

    /**
     * @Route("/films/category/all", name="app_films_category_all")
     */
    public function all(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/all.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/films/category/create", name="app_films_category_create")
     */
    public function create(Request $request, SluggerInterface $slug, EntityManagerInterface $entityManagerInterface): Response
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $slug->slug(strtolower($category->getName()));
            $category->setSlug($slug);

            $entityManagerInterface->persist($category);
            $entityManagerInterface->flush();

            return $this->redirectToRoute('app_films');
        }

        return $this->render('category/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function renderListCategory(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/_menu_category.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }
}
