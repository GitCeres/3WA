<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Comment;
use App\Form\RegisterType;
use App\Form\UserEditInfoType;
use App\Form\Model\ChangePassword;
use App\Form\ThemeType;
use App\Form\UserEditPasswordType;
use App\Form\UserDeleteAccountType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    /**
     * Register page
     * 
     * @Route("/register", name="app_register")
     */
    public function register(EntityManagerInterface $entityManagerInterface, Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User;

        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $user->getPassword();

            $user->setPassword($passwordHasher->hashPassword($user, $password));

            $user = $user->setRoles([User::ROLE_USER]);
            $user = $user->setMode(User::MODE_LIGHT);

            $entityManagerInterface->persist($user);
            $entityManagerInterface->flush();

            $this->addFlash("success", sprintf("Votre compte a bien été créé %s", $user->getFullName()));

            return $this->redirectToRoute('homepage');
        }

        return $this->renderForm('user/register.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * Account user
     * 
     * @Route("/account", name="app_account")
     */
    public function account(): Response
    {
        return $this->render('user/account.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    /**
     * Change user firstName, lastName, email and gender
     * 
     * @Route("/account/{name}/info", name="app_user_info")
     */
    public function info(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(UserEditInfoType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $entityManagerInterface->persist($user);
            $entityManagerInterface->flush();

            $this->addFlash("success", sprintf("Votre compte a bien été mis à jour %s", $user->getFullName()));

            return $this->redirectToRoute('app_account');
        }

        return $this->renderForm('user/info.html.twig', [
            'form' => $form,
            'user' => $user,
        ]);
    }

    /**
     * Change user password
     * 
     * @Route("/account/{name}/password", name="app_user_password")
     */
    public function changePassword(Request $request, EntityManagerInterface $entityManagerInterface, UserPasswordHasherInterface $passwordHasher): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $changePassword = new ChangePassword;

        $form = $this->createForm(UserEditPasswordType::class, $changePassword);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $password = $form->get('newPassword')->getData();
            
            $user->setPassword($passwordHasher->hashPassword($user, $password));

            $entityManagerInterface->persist($user);
            $entityManagerInterface->flush();

            $this->addFlash("success", sprintf("Votre mot de passe a bien été mis à jour %s", $user->getFullName()));

            return $this->redirectToRoute('app_account');
        }

        return $this->renderForm('user/password.html.twig', [
            'form' => $form,
            'user' => $user,
        ]);
    }

    /**
     * Show user comments
     * 
     * @Route("/account/{name}/comments", name="app_user_comments")
     */
    public function showComments(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('user/comments.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * Delete one user comment
     * 
     * @Route("/account/{name}/delete/comment/{id}", name="app_user_delete_comment")
     */
    public function deleteComment(Comment $comment, EntityManagerInterface $entityManagerInterface): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        
        $entityManagerInterface->remove($comment);
        $entityManagerInterface->flush();
    
        $this->addFlash("success", "Votre commentaire a bien été supprimé");

        return $this->render('user/comments.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * Show user stars for each films
     * 
     * @Route("/account/{name}/stars", name="app_user_stars")
     */
    public function showStars(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('user/stars.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * Delete user account
     * 
     * @Route("/account/{name}/delete", name="app_user_delete")
     */
    public function deleteAccount(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(UserDeleteAccountType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManagerInterface->remove($user);
            $entityManagerInterface->flush();

            $this->addFlash("success", "Votre compte a bien été supprimé");

            $this->container->get('security.token_storage')->setToken(null);

            return $this->redirectToRoute('homepage');
        }

        return $this->renderForm('user/delete.html.twig', [
            'form' => $form,
            'user' => $user,
        ]);
    }

    /**
     * Change Theme mode
     * 
     * @Route("/account/{name}/theme", name="app_user_theme")
     */
    public function theme(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(ThemeType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $entityManagerInterface->persist($user);
            $entityManagerInterface->flush();

            $this->addFlash("success", sprintf("Vous utilisez désormais le thème %s", $user->getMode()));

            return $this->redirectToRoute('app_account');
        }

        return $this->renderForm('user/theme.html.twig', [
            'form' => $form,
            'user' => $user,
        ]);
    }
}
