<?php

namespace Oxa\VideoBundle\Uploader;

use Oxa\VideoBundle\Service\Model\VideoApiClientInterface;
use Oxa\VideoBundle\Service\VideoAPIClient;
use Oxa\VideoBundle\Uploader\Model\VideoFileUploaderInterface;

/**
 * Created by PhpStorm.
 * User: Xedin
 * Date: 11.06.16
 * Time: 22:36
 */
abstract class VideoFileUploader implements VideoFileUploaderInterface
{
    protected $requestData;

    protected $videoAPIClient;

    protected $apiPassword;

    protected $projectId;
    protected $useProjectId;

    public function __construct(
        VideoApiClientInterface $videoAPIClient,
        string $apiPassword,
        int $projectId,
        bool $useProjectId
    ) {
        $this->requestData = [];

        $this->videoAPIClient = $videoAPIClient;

        $this->apiPassword = $apiPassword;

        $this->projectId    = $projectId;
        $this->useProjectId = $useProjectId;
    }

    public function setData(array $data) : VideoFileUploaderInterface
    {
        $this->requestData = $data;
        return $this;
    }

    public function upload()
    {
        $requestData = $this->prepareRequestData();
        $result = $this->videoAPIClient->call(VideoApiClientInterface::HTTP_METHOD_POST, '', $requestData);

        return $result;
    }

    protected function getApiPassword()
    {
        return $this->apiPassword;
    }

    protected function getProjectId()
    {
        if ($this->useProjectId) {
            return $this->requestData['project_id'] ?? $this->projectId;
        }

        return 0;
    }

    abstract protected function prepareRequestData() : array;
}
