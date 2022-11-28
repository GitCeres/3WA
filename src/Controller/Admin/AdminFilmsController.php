<?php

namespace App\Controller\Admin;

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
     * @Route("/admin/film/show/{slug}", name="app_admin_film_show")
     */
    public function show($slug, FilmRepository $filmRepository, CommentRepository $commentRepository): Response
    {
        $film = $filmRepository->findOneBy([
            'slug' => $slug,
        ]);

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
     * @Route("/admin/film/edit/{slug}", name="app_admin_film_edit")
     */
    public function edit($slug, FilmRepository $filmRepository, Request $request, EntityManagerInterface $entityManagerInterface, SluggerInterface $slugger): Response
    {
        $film = $filmRepository->findOneBy([
            'slug' => $slug,
        ]);

        $form = $this->createForm(AdminEditFilmType::class, $film);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $slugger = $slugger->slug(strtolower($film->getTitle()));
            $film->setSlug($slugger);

            $entityManagerInterface->persist($film);
            $entityManagerInterface->flush();

            $this->addFlash("success", "Le film {$film->getTitle()} a bien été mis à jour ");

            return $this->redirectToRoute('app_admin_films');
        }

        return $this->render('admin/films/edit.html.twig', [
            'form' => $form->createView(),
            'film' => $film,
        ]);
    }

    /**
     * @Route("/admin/film/delete/{slug}", name="app_admin_film_delete")
     */
    public function delete($slug, FilmRepository $filmRepository, EntityManagerInterface $entityManagerInterface): Response
    {
        $film = $filmRepository->findOneBy(['slug' => $slug]);

        $entityManagerInterface->remove($film);
        $entityManagerInterface->flush();

        $this->addFlash("success", "Le film {$film->getTitle()} a bien été supprimée");

        return $this->redirectToRoute('app_admin_films');
    }
}
