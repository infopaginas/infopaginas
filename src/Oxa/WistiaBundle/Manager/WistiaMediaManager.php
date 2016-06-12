<?php
/**
 * Created by PhpStorm.
 * User: Xedin
 * Date: 11.06.16
 * Time: 22:35
 */

namespace Oxa\WistiaBundle\Manager;


use Oxa\WistiaBundle\Service\Model\WistiaApiClientInterface;

class WistiaMediaManager extends BaseWistiaAPIManager
{
    protected $endpointModule = 'medias';

    public function __construct(WistiaApiClientInterface $apiClient)
    {
        parent::__construct($apiClient);
    }

    public function stats(string $hash)
    {
        $endpoint = $this->getEndpointModule() . '/' . $hash . '/stats';
        return $this->doAPICall(WistiaApiClientInterface::HTTP_METHOD_GET, $endpoint);
    }
}