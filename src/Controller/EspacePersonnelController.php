<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Repository\ListeRepository;
use App\TMDB\TMDB;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



class EspacePersonnelController extends AbstractController
{
    #[Route('/user', name: 'user.index')]
    public function index(ListeRepository $listeRepository): Response
    {
        $seenListes = $listeRepository->findBy(['user' => $this->getUser(), 'name' => 'seen']);
        $laterListes = $listeRepository->findBy(['user' => $this->getUser(), 'name' => 'later']);
        $favListes = $listeRepository->findBy(['user' => $this->getUser(), 'name' => 'favoris']);

        $seenMovies = [];
        $seenSeries = [];
        $laterMovies = [];
        $laterSeries = [];
        $favMovies = [];
        $favSeries = [];

        if ($seenListes) {
            $i = 0;
            foreach ($seenListes as $seenListe) {
                if ($seenListe->getIdMovie()) {
                    $seenMovies += [$i => (new TMDB())->getMovieById($seenListe->getIdMovie(), 'movie')];
                }
                else {
                    $seenSeries += [$i => (new TMDB())->getMovieById($seenListe->getIdSerie(), 'tv')];
                }
                $i++;
            }
        }

        if ($laterListes) {
            $i = 0;
            foreach ($laterListes as $laterListe) {
                if ($laterListe->getIdMovie()) {
                    $laterMovies += [$i => (new TMDB())->getMovieById($laterListe->getIdMovie(), 'movie')];
                }
                else {
                    $laterSeries += [$i => (new TMDB())->getMovieById($laterListe->getIdSerie(), 'tv')];
                }
                $i++;
            }
        }

        if ($favListes) {
            $i = 0;
            foreach ($favListes as $favListe) {
                if ($favListe->getIdMovie()) {
                    $favMovies += [$i => (new TMDB())->getMovieById($favListe->getIdMovie(), 'movie')];
                }
                else {
                    $favSeries += [$i => (new TMDB())->getMovieById($favListe->getIdSerie(), 'tv')];
                }
                $i++;
            }
        }

        return $this->render('espace_personnel/index.html.twig', [
            'seenMovies' => $seenMovies,
            'seenSeries' => $seenSeries,
            'laterMovies' => $laterMovies,
            'laterSeries' => $laterSeries,
            'favMovies' => $favMovies,
            'favSeries' => $favSeries
        ]);
    }

    #[Route('/user/comment/delete/{id<\d+>}', name: 'user.comment.delete')]
    public function delete(Comment $comment, EntityManagerInterface $manager): Response
    {
        // On supprime en BDD
        $manager->remove($comment);
        $manager->flush();

        // Message flash
        $this->addFlash('success', 'Commentaire correctement supprimée.');

        // Redirection vers le dashboard admin
        return $this->redirectToRoute('user.index');
    }
}
