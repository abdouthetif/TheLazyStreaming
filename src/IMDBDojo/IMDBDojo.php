<?php


namespace App\IMDBDojo;


use GuzzleHttp\Client;

class IMDBDojo
{
    private $client;

    private $headers;

    /**
     * IMDB constructor.
     */
    public function __construct()
    {
        $this->client = new Client(['base_uri' => 'https://imdb8.p.rapidapi.com/']);
        $this->headers = [
            'x-rapidapi-key' => '983138431fmsh854a9a496b612bfp11dbd3jsn37d41c476296',
            'x-rapidapi-host' => 'imdb8.p.rapidapi.com'
        ];
    }

    public function getPlots(string $id)
    {
        $response = $this->client->request('GET','title/get-plots',
            [
                'query' => [
                    'tconst' => $id
                ],
                'headers' => $this->headers
            ]
        );

        $response = $response->getBody();

        return json_decode($response, true);
    }

    public function getTopCast(string $id)
    {
        $response = $this->client->request('GET','title/get-top-cast',
            [
                'query' => [
                    'tconst' => $id
                ],
                'headers' => $this->headers
            ]
        );

        $response = $response->getBody();

        return json_decode($response, true);
    }

    public function getTopCrew(string $id)
    {
        $response = $this->client->request('GET','title/get-top-crew',
            [
                'query' => [
                    'tconst' => $id
                ],
                'headers' => $this->headers
            ]
        );

        $response = $response->getBody();

        return json_decode($response, true);
    }

    public function getCharnameList(string $nameId, string $titleId)
    {
        $nameId = str_replace('/', '', $nameId);
        $nameId = str_replace('name', '', $nameId);

        $response = $this->client->request('GET','title/get-charname-list',
            [
                'query' => [
                    'id' => $nameId,
                    'tconst' => $titleId
                ],
                'headers' => $this->headers
            ]
        );

        $response = $response->getBody();

        return json_decode($response, true);
    }
}