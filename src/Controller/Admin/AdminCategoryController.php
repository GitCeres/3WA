<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\AdminEditCategoryType;
use App\Repository\CategoryRepository;
use App\Repository\FilmRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminCategoryController extends AbstractController
{
    /**
     * @Route("/admin/category", name="app_admin_category")
     */
    public function list(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findBy([], [
            'name' => 'ASC',
        ]);

        return $this->render('admin/category/list.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/admin/category/show/{slug}", name="app_admin_category_show")
     */
    public function show($slug, CategoryRepository $categoryRepository, FilmRepository $filmRepository): Response
    {
        $category = $categoryRepository->findOneBy([
            'slug' => $slug
        ]);

        $films = $filmRepository->findBy([
            'category' => $category
        ], [
            'createdAt' => 'DESC'
        ]);

        return $this->render('admin/category/show.html.twig', [
            'category' => $category,
            'films' => $films,
        ]);
    }

    /**
     * @Route("/admin/category/edit/{slug}", name="app_admin_category_edit")
     */
    public function edit($slug, CategoryRepository $categoryRepository, Request $request, EntityManagerInterface $entityManagerInterface, SluggerInterface $slugger): Response
    {
        $category = $categoryRepository->findOneBy([
            'slug' => $slug
        ]);

        $form = $this->createForm(AdminEditCategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $slugger = $slugger->slug(strtolower($category->getName()));
            $category->setSlug($slugger);

            $entityManagerInterface->persist($category);
            $entityManagerInterface->flush();

            $this->addFlash("success", "La catégorie {$category->getName()} a bien été mis à jour ");

            return $this->redirectToRoute('app_admin_category');
        }

        return $this->render('admin/category/edit.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
        ]);
    }

    /**
     * @Route("/admin/category/delete/{slug}", name="app_admin_category_delete")
     */
    public function delete($slug, CategoryRepository $categoryRepository, EntityManagerInterface $entityManagerInterface): Response
    {
        $category = $categoryRepository->findOneBy([
            'slug' => $slug
        ]);

        $entityManagerInterface->remove($category);
        $entityManagerInterface->flush();

        $this->addFlash("success", "La catégorie {$category->getName()} a bien été supprimée");

        return $this->redirectToRoute('app_admin_category');
    }
}
