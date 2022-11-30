<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCommentsController extends AbstractController
{
    /**
     * Show all comments with details on back office
     * 
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
     * Show one comment on back office
     * 
     * @Route("/admin/comment/show/{id}", name="app_admin_comment_show")
     */
    public function show(Comment $comment): Response
    {
        return $this->render('admin/comments/show.html.twig', [
            'comment' => $comment
        ]);
    }

    /**
     * Delete one comment on back office
     * 
     * @Route("/admin/comment/delete/{id}", name="app_admin_comment_delete")
     */
    public function delete(Comment $comment, EntityManagerInterface $entityManagerInterface): Response
    {
        $entityManagerInterface->remove($comment);
        $entityManagerInterface->flush();

        $this->addFlash("success", sprintf("Le commentaire de %s a bien été supprimée", $comment->getUser()->getFullName()));

        return $this->redirectToRoute('app_admin_comments');
    }
}
