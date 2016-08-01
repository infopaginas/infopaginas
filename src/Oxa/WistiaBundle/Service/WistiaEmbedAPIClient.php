<?php
/**
 * Created by PhpStorm.
 * User: Xedin
 * Date: 12.06.16
 * Time: 22:14
 */

namespace Oxa\WistiaBundle\Service;

use GuzzleHttp\Client;
use Oxa\WistiaBundle\Service\Model\WistiaApiClientInterface;
use Psr\Http\Message\ResponseInterface;

class WistiaEmbedAPIClient implements WistiaApiClientInterface
{
    private $httpClient;

    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function call(string $method, string $endpoint, array $data = []) : array
    {
        $requestURL = $this->buildRequestURL($endpoint, $data);

        $response = $this->getHttpClient()->request($method, $requestURL);

        return $this->buildResponseArray($response);
    }

    private function buildResponseArray(ResponseInterface $response) : array
    {
        $jsonResponse = $response->getBody()->getContents();
        return json_decode($jsonResponse, true);
    }

    private function buildRequestURL(string $endpoint, $data) : string
    {
        $data['url'] = $endpoint;
        return WistiaApiClientInterface::WISTIA_EMBED_API_URL . '?' . http_build_query($data);
    }

    private function getHttpClient() : Client
    {
        return $this->httpClient;
    }
}
