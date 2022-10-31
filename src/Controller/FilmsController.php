<?php

namespace App\Controller;

use App\Entity\Film;
use App\Form\FilmType;
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
    public function index(): Response
    {
        return $this->render('films/index.html.twig');
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

            return $this->redirectToRoute('homepage');
        }

        return $this->render('films/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
