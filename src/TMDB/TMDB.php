<?php


namespace App\TMDB;

use GuzzleHttp\Client;

class TMDB
{
    private $client;

    private $apiKey;

    private $language;

    private $adult;

    private $apiLang;

    /**
     * TMDB constructor.
     */
    public function __construct()
    {
        $this->apiKey = '658101b75409c7bfb6e075bb183eb3c2';
        $this->language = 'fr-FR';
        $this->adult = 'false';

        $this->apiLang = '?api_key='
                    . $this->apiKey
                    . '&language='
                    . $this->language
        ;

        $this->client = new Client(['base_uri' => 'https://api.themoviedb.org/3/']);
    }

    public function getKeywords(string $keyword)
    {
        $queries = preg_replace('/\s+/', '%20', $keyword);
        $response = $this->client->request('GET', 'search/keyword?api_key=' . $this->apiKey . '&query=' . $queries);
        $response = $response->getBody();

        $result = json_decode($response, true);
        $results = [];
        $index =  0;

        for ($i = 1; $i<=$result['total_pages']; $i++) {
            $response = $this->client->request('GET', 'search/keyword?api_key=' . $this->apiKey . '&query=' . $queries . '&page=' . $i);
            $response = $response->getBody();

            $json = json_decode($response, true);
            $results += [$index => $json['results']];
            $index++;
        }

        $reducedResults = array_reduce($results, 'array_merge', array());
        $results = '';

        foreach ($reducedResults as $reducedResult) {
            $results .= $reducedResult['id'] . '%7C';
        }

        return $results;
    }

    public function getCategories()
    {
        $resultArray = [];

        $response = $this->client->request('GET', 'genre/movie/list' . $this->apiLang);
        $response = $response->getBody();

        $json = json_decode($response, true);

        $categories = array_reduce($json, function ($ax, $dx) {return $dx;});

        foreach ($categories as $category) {
            $resultArray += [
                $category['name'] => $category['id']
            ];
        }

        return $resultArray;
    }

    public function getMovieById(int $id, string $type)
    {
        $response = $this->client->request('GET',
            $type
            . '/'
            . $id
            . $this->apiLang
        );

        $response = $response->getBody();

        return json_decode($response, true);
    }

    public function getResultByQuery(Array $searchParameter)
    {
        if ($searchParameter) {

            $year = $searchParameter['year']??'';
            $genre = $searchParameter['genre']??'';
            $keyword = $searchParameter['keywords']??'';
            $voteAverage = $searchParameter['rating']??'';

            $response = $this->client->request('GET',
                'discover/'
                . $searchParameter['type']
                . $this->apiLang
                . '&include_adult='
                . $this->adult
                . '&page=1'
                . '&primary_release_year='
                . $year
                . '&vote_average.gte='
                . $voteAverage
                . '&with_genres='
                . $genre
                . '&with_keywords='
                . $keyword
            );

            $response = $response->getBody();
            $json = json_decode($response, true);

            $response = $this->client->request('GET',
                'discover/'
                . $searchParameter['type']
                . $this->apiLang
                . '&include_adult='
                . $this->adult
                . '&page='
                . rand(1, $json['total_pages'])
                . '&primary_release_year='
                . $year
                . '&vote_average.gte='
                . $voteAverage
                . '&with_genres='
                . $genre
                . '&with_keywords='
                . $keyword
            );

            $response = $response->getBody();
            $results = json_decode($response, true);

            return $results['results'];
        }
    }
}