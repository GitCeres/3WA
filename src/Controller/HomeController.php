<?php

namespace App\Controller;

use App\Repository\FilmRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * Homepage
     * @Route("/", name="homepage")
     */
    public function index(FilmRepository $filmRepository): Response
    {
        $films = $filmRepository->findBy([], [
            'createdAt' => 'DESC'
        ], 2);

        return $this->render('home/home.html.twig', [
            'films' => $films,
        ]);
    }
}
