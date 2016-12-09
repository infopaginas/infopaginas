<?php
/**
 * Created by PhpStorm.
 * User: Xedin
 * Date: 11.06.16
 * Time: 22:35
 */

namespace Oxa\VideoBundle\Manager;

use Oxa\VideoBundle\Service\Model\VideoApiClientInterface;

class VideoEmbedAPIManager extends BaseVideoAPIManager
{
    const WISTIA_MEDIA_URL = 'https://support.wistia.com/medias/';

    protected $endpointModule = '';

    private $videoApiClient;

    public function __construct(VideoApiClientInterface $apiClient, string $apiPassword)
    {
        parent::__construct($apiClient, $apiPassword);

        $this->videoApiClient = $apiClient;
    }

    public function get(string $hash, array $dimensions = []) : array
    {
        $endpoint = self::WISTIA_MEDIA_URL . $hash;
        return $this->doAPICall(VideoApiClientInterface::HTTP_METHOD_GET, $endpoint, $dimensions);
    }

    protected function doAPICall(string $method, string $endpoint, array $data = [])
    {
        return $this->videoApiClient->call($method, $endpoint, $data);
    }
}
