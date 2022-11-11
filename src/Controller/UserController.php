<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Comment;
use App\Form\RegisterType;
use App\Form\UserEditInfoType;
use App\Form\Model\ChangePassword;
use App\Form\UserEditPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    /**
     * Page pour s'enregistrer
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

            $user = $user->setRoles(['ROLE_USER']);

            $entityManagerInterface->persist($user);
            $entityManagerInterface->flush();

            $this->addFlash("success", "Votre compte a bien été créé {$user->getFullName()}");

            return $this->redirectToRoute('homepage');
        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/account", name="app_account")
     */
    public function account(): Response
    {
        return $this->render('user/account.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    /**
     * @Route("/{name}/info", name="app_user_info")
     */
    public function info(Request $request, EntityManagerInterface $entityManagerInterface)
    {
        /** @var User */
        $user = $this->getUser();

        $form = $this->createForm(UserEditInfoType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $entityManagerInterface->persist($user);
            $entityManagerInterface->flush();

            $this->addFlash("success", "Votre compte a bien été mis à jour {$user->getFullName()}");

            return $this->redirectToRoute('app_account');
        }

        return $this->render('user/info.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{name}/password", name="app_user_password")
     */
    public function changePassword(Request $request, EntityManagerInterface $entityManagerInterface, UserPasswordHasherInterface $passwordHasher): Response
    {
        /** @var User */
        $user = $this->getUser();

        $changePassword = new ChangePassword;

        $form = $this->createForm(UserEditPasswordType::class, $changePassword);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $password = $form->get('newPassword')->getData();
            
            $user->setPassword($passwordHasher->hashPassword($user, $password));

            $entityManagerInterface->persist($user);
            $entityManagerInterface->flush();

            $this->addFlash("success", "Votre mot de passe a bien été mis à jour {$user->getFullName()}");

            return $this->redirectToRoute('app_account');
        }

        return $this->render('user/password.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{name}/comments", name="app_user_comments")
     */
    public function showComments(): Response
    {
        /** @var User */
        $user = $this->getUser();

        return $this->render('user/comments.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{name}/delete/comment/{id}", name="app_user_delete_comment")
     */
    public function deleteComment(Comment $comment, EntityManagerInterface $entityManagerInterface)
    {
        /** @var User */
        $user = $this->getUser();
        
        $entityManagerInterface->remove($comment);
        $entityManagerInterface->flush();
    
        $this->addFlash("success", "Votre commentaire a bien été supprimé");

        return $this->render('user/comments.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{name}/stars", name="app_user_stars")
     */
    public function showStars()
    {
        /** @var User */
        $user = $this->getUser();

        return $this->render('user/stars.html.twig', [
            'user' => $user,
        ]);
    }
}
