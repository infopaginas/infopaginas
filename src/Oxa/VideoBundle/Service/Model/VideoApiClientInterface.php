<?php
/**
 * Created by PhpStorm.
 * User: Xedin
 * Date: 12.06.16
 * Time: 11:31
 */

namespace Oxa\VideoBundle\Service\Model;

use GuzzleHttp\Client;

interface VideoApiClientInterface
{
    const HTTP_METHOD_GET    = 'GET';
    const HTTP_METHOD_POST   = 'POST';
    const HTTP_METHOD_PUT    = 'PUT';
    const HTTP_METHOD_DELETE = 'DELETE';

    const API_USER     = 'api';

    const RESPONSE_FORMAT   = 'json';

    const WISTIA_DATA_API_URL = 'https://api.wistia.com/v1/';
    const WISTIA_UPLOAD_API_URL = 'https://upload.wistia.com/';
    const WISTIA_EMBED_API_URL = 'http://fast.wistia.net/oembed/';

    public function __construct(Client $httpClient);

    public function call(string $method, string $endpoint, array $data);
}
