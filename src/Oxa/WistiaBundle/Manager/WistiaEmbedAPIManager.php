<?php
/**
 * Created by PhpStorm.
 * User: Xedin
 * Date: 11.06.16
 * Time: 22:35
 */

namespace Oxa\WistiaBundle\Manager;

use Oxa\WistiaBundle\Service\Model\WistiaApiClientInterface;

class WistiaEmbedAPIManager extends BaseWistiaAPIManager
{
    const WISTIA_MEDIA_URL = 'https://support.wistia.com/medias/';

    protected $endpointModule = '';

    private $wistiaApiClient;

    public function __construct(WistiaApiClientInterface $apiClient, string $apiPassword)
    {
        parent::__construct($apiClient, $apiPassword);

        $this->wistiaApiClient = $apiClient;
    }

    public function get(string $hash, array $dimensions = []) : array
    {
        $endpoint = self::WISTIA_MEDIA_URL . $hash;
        return $this->doAPICall(WistiaApiClientInterface::HTTP_METHOD_GET, $endpoint, $dimensions);
    }

    protected function doAPICall(string $method, string $endpoint, array $data = [])
    {
        return $this->wistiaApiClient->call($method, $endpoint, $data);
    }
}