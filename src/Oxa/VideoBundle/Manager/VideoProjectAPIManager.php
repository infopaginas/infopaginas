<?php
/**
 * Created by PhpStorm.
 * User: Xedin
 * Date: 11.06.16
 * Time: 22:35
 */

namespace Oxa\VideoBundle\Manager;

use Oxa\VideoBundle\Service\Model\VideoApiClientInterface;

class VideoProjectAPIManager extends BaseVideoAPIManager
{
    protected $endpointModule = 'projects';

    public function __construct(VideoApiClientInterface $apiClient, string $apiPassword)
    {
        parent::__construct($apiClient, $apiPassword);
    }

    public function create(array $data)
    {
        return $this->doAPICall(VideoApiClientInterface::HTTP_METHOD_POST, $this->getEndpointModule(), $data);
    }
}
