<?php


namespace App\IMDB;


use GuzzleHttp\Client;

class IMDB
{
    private $client;

    private $headers;

    /**
     * IMDB constructor.
     */
    public function __construct()
    {
        $this->client = new Client(['base_uri' => 'https://movies-tvshows-data-imdb.p.rapidapi.com/']);
        $this->headers = [
            'x-rapidapi-key' => '983138431fmsh854a9a496b612bfp11dbd3jsn37d41c476296',
            'x-rapidapi-host' => 'movies-tvshows-data-imdb.p.rapidapi.com'
        ];
    }

    public function getMovieById(string $id, string $type)
    {
        if ($type == 'tv') {
            $type = 'show';
        }

        $response = $this->client->request('GET','https://movies-tvshows-data-imdb.p.rapidapi.com/',
            [
                'query' => [
                    'type' => 'get-' . $type . '-details',
                    'imdb' => $id
                ],
                'headers' => $this->headers
            ]
        );

        $response = $response->getBody();

        return json_decode($response, true);
    }

    public function getRandomMovie()
    {
        $response = $this->client->request('GET','https://movies-tvshows-data-imdb.p.rapidapi.com/',
            [
                'query' => [
                    'type' => 'get-random-details',
                    'page' => '1'
                ],
                'headers' => $this->headers
            ]
        );

        $response = $response->getBody();

        return json_decode($response, true);
    }
}