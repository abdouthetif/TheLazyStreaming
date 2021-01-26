<?php

namespace App\Controller;

use App\Form\CommentType;
use App\Form\GetSearchType;
use App\IMDB\IMDB;
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
            $comment->setIsValid(false);

            $manager->persist($comment);
            $manager->flush();

            $this->addFlash('success', "Votre commentaire est créé avec succès. Il faudra attendre qu'un modérateur le valide.");

            // Redirection vers la page du film
            return $this->redirectToRoute('search.detailsDisplay', ['type' => $type, 'id' => $id]);
        }

        $tmdbDetails = (new TMDB())->getMovieById($id, $type);
        $imdbDetails = '';

        if (isset($tmdbDetails) && !empty($tmdbDetails['imdb_id'])) {

            $imdbDetails = (new IMDB())->getMovieById($tmdbDetails['imdb_id'], $type);
        }

        if ($type == 'movie') {

            $criteria = [
                'id_movie_tmdb' => $id,
                'is_valid' => true
            ];
        }
        elseif ($type == 'tv') {
            $criteria = [
                'id_serie_tmdb' => $id,
                'is_valid' => true
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
            'comments' => $comments
        ]);
    }
}
