<?php
/**
 * Created by PhpStorm.
 * User: Xedin
 * Date: 12.06.16
 * Time: 11:31
 */

namespace Oxa\WistiaBundle\Service\Model;


use GuzzleHttp\Client;

interface WistiaApiClientInterface
{
    const HTTP_METHOD_GET    = 'GET';
    const HTTP_METHOD_POST   = 'POST';
    const HTTP_METHOD_PUT    = 'PUT';
    const HTTP_METHOD_DELETE = 'DELETE';

    const API_USER     = 'api';
    const API_PASSWORD = 'ddd4273a9f4408e692633fea2bee70620d8982774ee82c44c9452263596e4d8c';

    const RESPONSE_FORMAT   = 'json';

    const WISTIA_DATA_API_URL = 'https://api.wistia.com/v1/';
    const WISTIA_UPLOAD_API_URL = 'https://upload.wistia.com/';

    public function __construct(Client $httpClient);

    public function call(string $method, string $endpoint, array $data);
}