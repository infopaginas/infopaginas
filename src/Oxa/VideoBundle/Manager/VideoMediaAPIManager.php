<?php
/**
 * Created by PhpStorm.
 * User: Xedin
 * Date: 11.06.16
 * Time: 22:35
 */

namespace Oxa\VideoBundle\Manager;

use Oxa\VideoBundle\Service\Model\VideoApiClientInterface;

class VideoMediaAPIManager extends BaseVideoAPIManager
{
    protected $endpointModule = 'medias';

    public function __construct(VideoApiClientInterface $apiClient, string $apiPassword)
    {
        parent::__construct($apiClient, $apiPassword);
    }

    public function stats(string $hash)
    {
        $endpoint = $this->getEndpointModule() . '/' . $hash . '/stats';
        return $this->doAPICall(VideoApiClientInterface::HTTP_METHOD_GET, $endpoint);
    }
}
