<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EscapeGameController extends AbstractController
{
    /**
     * Show part 1 escape game
     * 
     * @Route("/escape_game", name="app_escape_game")
     */
    public function index(): Response
    {
        return $this->render('escape_game/index.html.twig');
    }

    /**
     * Show part 2 escape game
     * @Route("/escape_game/next", name="app_escape_game_next")
     */
    public function next(Request $request): Response
    {
        $cookie = $request->cookies->get('escape-p1');

        if ($cookie) {
            return $this->render('escape_game/next.html.twig');
        }

        return $this->redirectToRoute('app_escape_game');
    }
}
