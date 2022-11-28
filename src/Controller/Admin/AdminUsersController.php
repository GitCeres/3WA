<?php

namespace App\Controller\Admin;

use App\Form\AdminDeleteUserType;
use App\Form\AdminEditUserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminUsersController extends AbstractController
{
    /**
     * @Route("/admin/users", name="app_admin_users")
     */
    public function list(UserRepository $UserRepository): Response
    {
        $users = $UserRepository->findBy([], [
            'lastName' => 'ASC',
        ]);

        return $this->render('admin/users/list.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/admin/user/show/{id}", name="app_admin_user_show")
     */
    public function show($id, UserRepository $userRepository)
    {
        $user = $userRepository->findOneBy([
            'id' => $id
        ]);
        
        return $this->render('admin/users/show.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/admin/user/edit/{id}", name="app_admin_user_edit")
     */
    public function edit($id, UserRepository $userRepository, Request $request, EntityManagerInterface $entityManagerInterface)
    {
        $user = $userRepository->findOneBy([
            'id' => $id
        ]);

        $form = $this->createForm(AdminEditUserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $entityManagerInterface->persist($user);
            $entityManagerInterface->flush();

            $this->addFlash("success", "Le compte de l'utilisateur {$user->getFullName()} a bien été mis à jour");

            return $this->redirectToRoute('app_admin_users');
        }

        return $this->render('admin/users/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    /**
     * @Route("/admin/user/delete/{id}", name="app_admin_user_delete")
     */
    public function delete($id, UserRepository $userRepository, Request $request, EntityManagerInterface $entityManagerInterface)
    {
        $user = $userRepository->findOneBy([
            'id' => $id
        ]);

        $form = $this->createForm(AdminDeleteUserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManagerInterface->remove($user);
            $entityManagerInterface->flush();

            $this->addFlash("success", "Le compte de l'utilisateur {$user->getFullName()} a bien été supprimé");

            return $this->redirectToRoute('app_admin_users');
        }

        return $this->render('admin/users/delete.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
}
