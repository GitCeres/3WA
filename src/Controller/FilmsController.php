<?php

namespace App\Controller;

use App\Entity\Film;
use App\Form\FilmType;
use App\Repository\FilmRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class FilmsController extends AbstractController
{
    /**
     * @Route("/films", name="app_films")
     */
    public function index(FilmRepository $filmRepository): Response
    {
        $films = $filmRepository->findBy([], ['createdAt' => 'DESC'], 3);

        return $this->render('films/index.html.twig', [
            'films' => $films
        ]);
    }

    /**
     * @Route("/films/create", name="app_films_create")
     */
    public function create(Request $request, SluggerInterface $slug, EntityManagerInterface $entityManagerInterface): Response
    {
        $film = new Film();

        $form = $this->createForm(FilmType::class, $film);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $slug->slug(strtolower($film->getTitle()));
            $film->setSlug($slug);

            $date = new DateTimeImmutable();
            $film->setCreatedAt($date);

            $entityManagerInterface->persist($film);
            $entityManagerInterface->flush();

            return $this->redirectToRoute('app_films');
        }

        return $this->render('films/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/films/show/{slug}", name="app_films_show")
     */
    public function show($slug, FilmRepository $filmRepository)
    {
        $film = $filmRepository->findOneBy([
            'slug' => $slug
        ]);

        return $this->render('films/show.html.twig', [
            'film' => $film
        ]);
    }

    /**
     * @Route("/films/all", name="app_films_all")
     */
    public function allMovies(FilmRepository $filmRepository)
    {
        $films = $filmRepository->findBy([], ['createdAt' => 'DESC']);

        return $this->render('films/all.html.twig', [
            'films' => $films
        ]);
    }
}
