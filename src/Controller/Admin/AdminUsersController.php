<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\AdminEditUserType;
use App\Form\AdminDeleteUserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminUsersController extends AbstractController
{
    /**
     * Show all users with details on back office
     * 
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
     * Show one user on back office
     * 
     * @Route("/admin/user/show/{id}", name="app_admin_user_show")
     */
    public function show(User $user)
    {
        return $this->render('admin/users/show.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * Edit one user on back office
     * 
     * @Route("/admin/user/edit/{id}", name="app_admin_user_edit")
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(User $user, Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $form = $this->createForm(AdminEditUserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $entityManagerInterface->persist($user);
            $entityManagerInterface->flush();

            $this->addFlash("success", sprintf("Le compte de l'utilisateur %s a bien été mis à jour", $user->getFullName()));

            return $this->redirectToRoute('app_admin_users');
        }

        return $this->renderForm('admin/users/edit.html.twig', [
            'form' => $form,
            'user' => $user,
        ]);
    }

    /**
     * Delete one user on back office
     * 
     * @Route("/admin/user/delete/{id}", name="app_admin_user_delete")
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(User $user, Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $form = $this->createForm(AdminDeleteUserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManagerInterface->remove($user);
            $entityManagerInterface->flush();

            $this->addFlash("success", sprintf("Le compte de l'utilisateur %s a bien été supprimé", $user->getFullName()));

            return $this->redirectToRoute('app_admin_users');
        }

        return $this->renderForm('admin/users/delete.html.twig', [
            'form' => $form,
            'user' => $user,
        ]);
    }
}
