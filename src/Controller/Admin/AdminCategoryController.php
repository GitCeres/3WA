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
     * Show all categories with details on back office
     * 
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
     * Show one category on back office
     * 
     * @Route("/admin/category/show/{slug}", name="app_admin_category_show")
     */
    public function show(FilmRepository $filmRepository, Category $category): Response
    {
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
     * Edit one category on back office
     * 
     * @Route("/admin/category/edit/{slug}", name="app_admin_category_edit")
     */
    public function edit(Request $request, EntityManagerInterface $entityManagerInterface, SluggerInterface $slugger, Category $category): Response
    {
        $form = $this->createForm(AdminEditCategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $slugger = $slugger->slug(strtolower($category->getName()));
            $category->setSlug($slugger);

            $entityManagerInterface->persist($category);
            $entityManagerInterface->flush();

            $this->addFlash("success", sprintf("La catégorie %s a bien été mis à jour ", $category->getName()));

            return $this->redirectToRoute('app_admin_category');
        }

        return $this->renderForm('admin/category/edit.html.twig', [
            'form' => $form,
            'category' => $category,
        ]);
    }

    /**
     * Delete one category on back office
     * 
     * @Route("/admin/category/delete/{slug}", name="app_admin_category_delete")
     */
    public function delete(Category $category, EntityManagerInterface $entityManagerInterface): Response
    {
        $entityManagerInterface->remove($category);
        $entityManagerInterface->flush();

        $this->addFlash("success", sprintf("La catégorie %s a bien été supprimée", $category->getName()));

        return $this->redirectToRoute('app_admin_category');
    }
}
