<?php
/**
 * Created by PhpStorm.
 * User: Xedin
 * Date: 11.06.16
 * Time: 22:35
 */

namespace Oxa\WistiaBundle\Manager;


use Oxa\WistiaBundle\Service\WistiaAPIClient;

class WistiaMediaManager extends BaseWistiaAPIManager
{
    protected $endpointModule = 'medias';

    public function __construct(WistiaAPIClient $apiClient)
    {
        parent::__construct($apiClient);
    }

    public function stats(string $hash)
    {
        $endpoint = $this->getEndpointModule() . '/' . $hash . '/stats';
        return $this->doAPICall(WistiaAPIClient::HTTP_METHOD_GET, $endpoint);
    }
}