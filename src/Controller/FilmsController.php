<?php

namespace App\Controller;

use App\Entity\Film;
use App\Entity\Stars;
use App\Form\FilmType;
use DateTimeImmutable;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Form\StarsType;
use App\Repository\FilmRepository;
use App\Repository\StarsRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    public function show($slug, FilmRepository $filmRepository, StarsRepository $starsRepository, CommentRepository $commentRepository, Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $film = $filmRepository->findOneBy([
            'slug' => $slug,
        ]);

        $comments = $commentRepository->findBy([
            'film' => $film,
        ], [
            'createdAt' => 'DESC'
        ]);

        $stars = new Stars();
        
        $formStars = $this->createForm(StarsType::class, $stars);

        $formStars->handleRequest($request);

        if ($formStars->isSubmitted() && $formStars->isValid()) {

            $stars->setUser($this->getUser());

            $stars->setFilm($film);

            $existingStars = $starsRepository->findOneBy([
                'user' => $this->getUser(),
                'film' => $film,
            ]);

            if (!$existingStars) {
                $entityManagerInterface->persist($stars);
            } else {
                $existingStars->setNumber($formStars->getData()->getNumber());
            }

            $entityManagerInterface->flush();

            return $this->redirectToRoute('app_films_show', [
                'slug' => $film->getSlug()
            ]);
        }

        $comment = new Comment();

        $formComment = $this->createForm(CommentType::class, $comment);

        $formComment->handleRequest($request);

        if ($formComment->isSubmitted() && $formComment->isValid()) {
            
            $comment->setUser($this->getUser());

            $comment->setFilm($film);

            $date = new DateTimeImmutable();
            $comment->setCreatedAt($date);

            $entityManagerInterface->persist($comment);
            $entityManagerInterface->flush();

            return $this->redirectToRoute('app_films_show', [
                'slug' => $film->getSlug()
            ]);
        }

        return $this->render('films/show.html.twig', [
            'film' => $film,
            'comments' => $comments,
            'formStars' => $formStars->createView(),
            'formComment' => $formComment->createView(),
        ]);
    }

    /**
     * @Route("/films/all", name="app_films_all")
     */
    public function allMovies(FilmRepository $filmRepository): Response
    {
        $films = $filmRepository->findBy([], ['createdAt' => 'DESC']);

        return $this->render('films/all.html.twig', [
            'films' => $films
        ]);
    }

    /**
     * @Route("/films/delete/comment/{id}", name="app_films_delete_comment")
     */
    public function deleteComment(Comment $comment, EntityManagerInterface $entityManagerInterface)
    {    
        $filmSlug = $comment->getFilm()->getSlug();
    
        $entityManagerInterface->remove($comment);
        $entityManagerInterface->flush();
    
        $this->addFlash("success", "Votre commentaire a bien été supprimé");
    
        return $this->redirectToRoute('app_films_show', [
            'slug' => $filmSlug,
        ]);
    }
}