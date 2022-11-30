<?php

namespace App\Controller\Admin;

use App\Entity\Film;
use App\Form\AdminEditFilmType;
use App\Repository\FilmRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminFilmsController extends AbstractController
{
    /**
     * Show all films with details on back office
     * 
     * @Route("/admin/films", name="app_admin_films")
     */
    public function list(FilmRepository $filmRepository): Response
    {
        $films = $filmRepository->findBy([], [
            'title' => 'ASC',
        ]);

        return $this->render('admin/films/list.html.twig', [
            'films' => $films,
        ]);
    }

    /**
     * Show one film on back office
     * 
     * @Route("/admin/film/show/{slug}", name="app_admin_film_show")
     */
    public function show(Film $film, CommentRepository $commentRepository): Response
    {
        $comments = $commentRepository->findBy([
            'film' => $film,
        ], [
            'createdAt' => 'DESC'
        ]);

        return $this->render('admin/films/show.html.twig', [
            'film' => $film,
            'comments' => $comments,
        ]);
    }

    /**
     * Edit one film on back office
     * 
     * @Route("/admin/film/edit/{slug}", name="app_admin_film_edit")
     */
    public function edit(Film $film, Request $request, EntityManagerInterface $entityManagerInterface, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(AdminEditFilmType::class, $film);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $slugger = $slugger->slug(strtolower($film->getTitle()));
            $film->setSlug($slugger);

            $entityManagerInterface->persist($film);
            $entityManagerInterface->flush();

            $this->addFlash("success", sprintf("Le film %s a bien été mis à jour ", $film->getTitle()));

            return $this->redirectToRoute('app_admin_films');
        }

        return $this->renderForm('admin/films/edit.html.twig', [
            'form' => $form,
            'film' => $film,
        ]);
    }

    /**
     * Delete one film on back office
     * 
     * @Route("/admin/film/delete/{slug}", name="app_admin_film_delete")
     */
    public function delete(Film $film, EntityManagerInterface $entityManagerInterface): Response
    {
        $entityManagerInterface->remove($film);
        $entityManagerInterface->flush();

        $this->addFlash("success", sprintf("Le film %s a bien été supprimée", $film->getTitle()));

        return $this->redirectToRoute('app_admin_films');
    }
}
