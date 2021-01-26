<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {

        $this->manager = $manager;
    }

    #[Route('/admin', name: 'admin.index')]
    public function index(CommentRepository $commentRepository): Response
    {
        return $this->render('admin/index.html.twig', [
            'validComments' => $commentRepository->findBy(['is_valid' => true], ['createdAt' => 'DESC']),
            'unvalidComments' => $commentRepository->findBy(['is_valid' => false], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/admin/comment/delete/{id<\d+>}', name: 'admin.comment.delete')]
    public function delete(Comment $comment): Response
    {
        // On persiste en BDD
        $this->manager->remove($comment);
        $this->manager->flush();

        // Message flash
        $this->addFlash('success', 'Commentaire correctement supprimée.');

        // Redirection vers le dashboard admin
        return $this->redirectToRoute('admin.index');
    }

    #[Route('/admin/comment/validate/{id<\d+>}', name: 'admin.comment.validate')]
    public function validate(Comment $comment): Response
    {
        $comment->setIsValid(true);

        $this->manager->flush();

        // Message flash
        $this->addFlash('success', 'Commentaire correctement validé.');

        // Redirection vers le dashboard admin
        return $this->redirectToRoute('admin.index');
    }
}
