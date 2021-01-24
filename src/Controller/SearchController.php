<?php

namespace App\Controller;

use App\Form\GetSearchType;
use App\IMDB\IMDB;
use App\TMDB\TMDB;
use GuzzleHttp\Client;
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
    public function detailsDisplay(Request $request): Response
    {
        if (isset($_POST)) {
            if (!empty($_POST['id_movie'])) {
                $type = 'movie';
            }
            else {
                $type = 'tv';
            }

            $tmdbDetails = (new TMDB())->getMovieById($_POST['id_movie'], $type);
            $imdbDetails = '';

            if (isset($tmdbDetails) && !empty($tmdbDetails['imdb_id'])) {

                $imdbDetails = (new IMDB())->getMovieById($tmdbDetails['imdb_id'], $type);
            }
        }

        $test = (new IMDB())->getRandomMovie();

        return $this->render('search/details.html.twig', [
            'tmdbDetails' => $tmdbDetails,
            'imdbDetails' => $imdbDetails
        ]);
    }
}
