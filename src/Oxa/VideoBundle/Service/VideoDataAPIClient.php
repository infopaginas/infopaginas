<?php
/**
 * Created by PhpStorm.
 * User: Xedin
 * Date: 12.06.16
 * Time: 11:33
 */

namespace Oxa\VideoBundle\Service;

use GuzzleHttp\Client;
use Oxa\VideoBundle\Service\Model\VideoApiClientInterface;
use Psr\Http\Message\ResponseInterface;

class VideoDataAPIClient implements VideoApiClientInterface
{
    private $httpClient;

    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function call(string $method, string $endpoint, array $data = []) : array
    {
        $requestURL = $this->buildRequestURL($endpoint);

        $response = $this->getHttpClient()->request($method, $requestURL, $data);

        return $this->buildResponseArray($response);
    }

    private function buildResponseArray(ResponseInterface $response) : array
    {
        $jsonResponse = $response->getBody()->getContents();
        return json_decode($jsonResponse, true);
    }

    private function buildRequestURL(string $endpoint) : string
    {
        return VideoApiClientInterface::WISTIA_DATA_API_URL . $endpoint . '.' . self::RESPONSE_FORMAT;
    }

    private function getHttpClient() : Client
    {
        return $this->httpClient;
    }
}
