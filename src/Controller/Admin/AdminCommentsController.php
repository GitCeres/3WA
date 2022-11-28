<?php

namespace App\Controller\Admin;

use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCommentsController extends AbstractController
{
    /**
     * @Route("/admin/comments", name="app_admin_comments")
     */
    public function list(CommentRepository $commentRepository): Response
    {
        $comments = $commentRepository->findBy([], [
            'createdAt' => 'DESC',
        ]);

        return $this->render('admin/comments/list.html.twig', [
            'comments' => $comments,
        ]);
    }

    /**
     * @Route("/admin/comment/show/{id}", name="app_admin_comment_show")
     */
    public function show($id, CommentRepository $commentRepository)
    {
        $comment = $commentRepository->findOneBy([
            'id' => $id
        ]);
        
        return $this->render('admin/comments/show.html.twig', [
            'comment' => $comment
        ]);
    }

    /**
     * @Route("/admin/comment/delete/{id}", name="app_admin_comment_delete")
     */
    public function delete($id, CommentRepository $commentRepository, EntityManagerInterface $entityManagerInterface): Response
    {
        $comment = $commentRepository->findOneBy([
            'id' => $id
        ]);

        $entityManagerInterface->remove($comment);
        $entityManagerInterface->flush();

        $this->addFlash("success", "Le commentaire de {$comment->getUser()->getFullName()} a bien été supprimée");

        return $this->redirectToRoute('app_admin_comments');
    }
}
