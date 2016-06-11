<?php
/**
 * Created by PhpStorm.
 * User: Xedin
 * Date: 11.06.16
 * Time: 22:35
 */

namespace Oxa\WistiaBundle\Manager;

use Oxa\WistiaBundle\Service\WistiaAPIClient;

class WistiaProjectManager extends BaseWistiaAPIManager
{
    protected $endpointModule = 'projects';

    public function __construct(WistiaAPIClient $apiClient)
    {
        parent::__construct($apiClient);
    }

    public function create(array $data)
    {
        return $this->doAPICall(WistiaAPIClient::HTTP_METHOD_POST, $this->getEndpointModule(), $data);
    }
}