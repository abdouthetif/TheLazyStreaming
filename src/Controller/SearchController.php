<?php

namespace App\Controller;

use App\Form\CommentType;
use App\Form\GetSearchType;
use App\IMDB\IMDB;
use App\IMDBDojo\IMDBDojo;
use App\Repository\CommentRepository;
use App\TMDB\TMDB;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'search.index')]
    public function index(): Response
    {
        $form = $this->createForm(GetSearchType::class);

        return $this->render('search/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/result', name: 'search.resultDisplay')]
    public function resultDisplay(Request $request): Response
    {
        $search = $_POST['get_search'];

        // @TODO Ajout vérification si user a bien choisi movie ou série

        if (!empty($search['keyword'])) {
            $keywords = (new TMDB())->getKeywords($search['keyword']);
        }

        $searchParameter = ['genre' => $search['genre']??''];
        $searchParameter += ['rating' => $search['rating']??''];
        $searchParameter += ['year' => $search['year']??''];
        $searchParameter += ['keywords' => $keywords??''];

        if (isset($search['movie'])) {

            $searchParameter += ['type' => 'movie'];
        }
        else {
            $searchParameter += ['type' => 'tv'];
        }

        $results = (new TMDB())->getResultByQuery($searchParameter);
        $resultID = array_rand($results, 1);
        $result = $results[$resultID];

        return $this->render('search/result.html.twig', [
            'result' => $result
        ]);
    }

    #[Route('/result/details', name: 'search.detailsDisplay')]
    public function detailsDisplay(Request $request, EntityManagerInterface $manager, CommentRepository $commentRepository): Response
    {
        if (!isset($_GET) && !array_key_exists('id', $_GET) && !array_key_exists('type', $_GET)) {
            echo "Erreur l'id du film ou de la série n'existe pas";
            exit;
        }

        $type = $_GET['type'];
        $id = $_GET['id'];

        $form = $this->createForm(CommentType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $comment->setUser($this->getUser());

            $manager->persist($comment);
            $manager->flush();

            $this->addFlash('success', 'Votre commentaire est créé avec succès.');
        }

        $tmdbDetails = (new TMDB())->getMovieById($id, $type);
        $imdbDetails = '';

        if (isset($tmdbDetails) && !empty($tmdbDetails['imdb_id'])) {

            $imdbDetails = (new IMDB())->getMovieById($tmdbDetails['imdb_id'], $type);
            $topCasts = (new IMDBDojo())->getTopCast($tmdbDetails['imdb_id']);
            $topCrew = (new IMDBDojo())->getTopCrew($tmdbDetails['imdb_id']);

            $topCastDetails = [];
            $directorDetails = [];
            $writerDetails = [];

            for ($i=0; $i<count($topCasts); $i++) {
                $topCastDetails += [$i => (new IMDBDojo())->getCharnameList($topCasts[$i], $tmdbDetails['imdb_id'])];
            }
            for ($i=0; $i<count($topCrew['directors']); $i++) {
                $directorDetails += [$i => (new IMDBDojo())->getCharnameList($topCrew['directors'][$i]['id'], $tmdbDetails['imdb_id'])];
            }
            for ($i=0; $i<count($topCrew['writers']); $i++) {
                $writerDetails += [$i => (new IMDBDojo())->getCharnameList($topCrew['writers'][$i]['id'], $tmdbDetails['imdb_id'])];
            }
        }

        if ($type == 'movie') {

            $criteria = [
                'id_movie_tmdb' => $id
            ];
        }
        elseif ($type == 'tv') {
            $criteria = [
                'id_serie_tmdb' => $id
            ];
        }

        $comments = $commentRepository->findBy($criteria, ['createdAt' => 'DESC'])??'';

        // $test = (new IMDB())->getRandomMovie();

        return $this->render('search/details.html.twig', [
            'tmdbDetails' => $tmdbDetails,
            'imdbDetails' => $imdbDetails,
            'form' => $form->createView(),
            'type' => $type,
            'id' => $id,
            'comments' => $comments,
            'directorDetails' => $directorDetails,
            'writerDetails' => $writerDetails
        ]);
    }
}
