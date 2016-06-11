<?php

namespace Oxa\WistiaBundle\Service;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

/**
 * Created by PhpStorm.
 * User: Xedin
 * Date: 11.06.16
 * Time: 22:37
 */
class WistiaAPIClient
{
    const HTTP_METHOD_GET    = 'GET';
    const HTTP_METHOD_POST   = 'POST';
    const HTTP_METHOD_PUT    = 'PUT';
    const HTTP_METHOD_DELETE = 'DELETE';

    const API_USER     = 'api';
    const API_PASSWORD = 'ddd4273a9f4408e692633fea2bee70620d8982774ee82c44c9452263596e4d8c';

    const RESPONSE_FORMAT   = 'json';

    const WISTIA_BASE_URL = "https://api.wistia.com/v1/";

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

    private function buildRequestURL(string $endpoint)
    {
        return self::WISTIA_BASE_URL . $endpoint . '.' . self::RESPONSE_FORMAT;
    }

    private function getHttpClient()
    {
        return $this->httpClient;
    }
}
