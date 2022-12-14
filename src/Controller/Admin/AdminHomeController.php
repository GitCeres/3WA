<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminHomeController extends AbstractController
{
    /**
     * Homepage for back office
     * @Route("/admin", name="app_admin")
     */
    public function index(): Response
    {
        return $this->render('admin/home/home.html.twig');
    }
}
