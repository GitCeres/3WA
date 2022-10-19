<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EscapeGameController extends AbstractController
{
    /**
     * @Route("/escape_game", name="app_escape_game")
     */
    public function index()
    {
        return $this->render('escape_game/index.html.twig');
    }

    /**
     * @Route("/escape_game/suite", name="app_escape_game_suite")
     */
    public function suite(Request $request)
    {
        $cookie = $request->cookies->get('escape-p1');

        if ($cookie) {
            return $this->render('escape_game/suite.html.twig');
        }

        return $this->redirectToRoute('app_escape_game');
    }
}
