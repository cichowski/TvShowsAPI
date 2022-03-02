<?php
namespace App\Library;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class GuzzleWrapper
{
    /**
     * @var GuzzleResponse
     */
    private $response;
    /**
     * @var GuzzleException
     */
    private $exception;
    /**
     * @var Client
     */
    private $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $uri
     */
    public function sendRequest(string $uri): void
    {
        unset($this->response, $this->exception);

        try {
            $this->response = $this->client->get($uri);
        }
        catch(GuzzleException $exception) {
            $this->exception = $exception;
        }
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return !empty($this->response) && empty($this->exception);
    }

    /**
     * @return bool
     */
    public function isFailure(): bool
    {
        return !empty($this->exception);
    }

    /**
     * @return HttpResponse
     */
    public function getResponse(): HttpResponse
    {
        if ($this->isSuccess()) {
            return $this->buildSuccessResponse();
        } elseif ($this->isFailure()) {
            return $this->buildErrorResponse();
        } else {
            throw new RuntimeException('Guzzle result is inconsistent');
        }
    }

    /**
     * @return HttpResponse
     */
    private function buildSuccessResponse(): HttpResponse
    {
        return new HttpResponse(
            $this->response->getBody()->getContents(),
            HttpResponse::HTTP_OK,
            $this->response->getHeaders()
        );
    }

    /**
     * @return HttpResponse
     */
    private function buildErrorResponse(): HttpResponse
    {
        return new HttpResponse(
            $this->exception->getMessage(),
            HttpResponse::HTTP_BAD_REQUEST
        );
    }
}