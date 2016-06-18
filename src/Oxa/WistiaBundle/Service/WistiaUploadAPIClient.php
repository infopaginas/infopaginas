<?php
/**
 * Created by PhpStorm.
 * User: Xedin
 * Date: 12.06.16
 * Time: 11:33
 */

namespace Oxa\WistiaBundle\Service;

use GuzzleHttp\Client;
use Oxa\WistiaBundle\Service\Model\WistiaApiClientInterface;
use Psr\Http\Message\ResponseInterface;

class WistiaUploadAPIClient implements WistiaApiClientInterface
{
    private $httpClient;

    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function call(string $method, string $endpoint, array $data = []) : array
    {
        $requestURL = WistiaApiClientInterface::WISTIA_UPLOAD_API_URL;

        $response = $this->getHttpClient()->request($method, $requestURL, $data);

        return $this->buildResponseArray($response);
    }

    private function buildResponseArray(ResponseInterface $response) : array
    {
        $jsonResponse = $response->getBody()->getContents();
        return json_decode($jsonResponse, true);
    }

    private function getHttpClient() : Client
    {
        return $this->httpClient;
    }
}
