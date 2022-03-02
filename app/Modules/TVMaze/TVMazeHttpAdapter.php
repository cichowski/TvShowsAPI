<?php
namespace App\Modules\TVMaze;

use App\Library\GuzzleWrapper;
use Symfony\Component\HttpFoundation\Response;

class TVMazeHttpAdapter
{
    private const API_URL = 'http://api.tvmaze.com/';
    private const API_SEARCH_ENDPOINT = 'search/shows?q=';

    /**
     * @var GuzzleWrapper
     */
    private $httpClient;

    public function __construct(GuzzleWrapper $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @param string $searchPhrase
     * @return array
     * @throws TVMazeResponseException
     */
    public function searchTVShow(string $searchPhrase): array
    {
        return $this->getRawContentFromResponse(
            $this->getResponseOrFail(self::API_URL . self::API_SEARCH_ENDPOINT . $searchPhrase)
        );
    }

    /**
     * @param string $url
     * @return Response
     * @throws TVMazeResponseException
     */
    private function getResponseOrFail(string $url): Response
    {
        $this->httpClient->sendRequest($url);
        if ($this->httpClient->isFailure()) {
            throw new TVMazeResponseException(
                $this->httpClient->getResponse()->getContent(),
                $this->httpClient->getResponse()->getStatusCode()
            );
        }

        return $this->httpClient->getResponse();
    }

    /**
     * @param Response $response
     * @return array
     */
    private function getRawContentFromResponse(Response $response): array
    {
        return (array)json_decode($response->getContent());
    }
}