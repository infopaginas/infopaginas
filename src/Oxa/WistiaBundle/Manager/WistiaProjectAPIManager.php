<?php
/**
 * Created by PhpStorm.
 * User: Xedin
 * Date: 11.06.16
 * Time: 22:35
 */

namespace Oxa\WistiaBundle\Manager;

use Oxa\WistiaBundle\Service\Model\WistiaApiClientInterface;

class WistiaProjectAPIManager extends BaseWistiaAPIManager
{
    protected $endpointModule = 'projects';

    public function __construct(WistiaApiClientInterface $apiClient, string $apiPassword)
    {
        parent::__construct($apiClient, $apiPassword);
    }

    public function create(array $data)
    {
        return $this->doAPICall(WistiaApiClientInterface::HTTP_METHOD_POST, $this->getEndpointModule(), $data);
    }
}
