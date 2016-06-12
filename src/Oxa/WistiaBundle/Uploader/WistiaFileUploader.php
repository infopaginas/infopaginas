<?php

namespace Oxa\WistiaBundle\Uploader;
use Oxa\WistiaBundle\Service\Model\WistiaApiClientInterface;
use Oxa\WistiaBundle\Service\WistiaAPIClient;
use Oxa\WistiaBundle\Uploader\Model\WistiaFileUploaderInterface;

/**
 * Created by PhpStorm.
 * User: Xedin
 * Date: 11.06.16
 * Time: 22:36
 */
abstract class WistiaFileUploader implements WistiaFileUploaderInterface
{
    protected $requestData;

    protected $wistiaAPIClient;

    protected $apiPassword;

    protected $projectId;
    protected $useProjectId;

    public function __construct(
        WistiaApiClientInterface $wistiaAPIClient,
        string $apiPassword,
        int $projectId,
        bool $useProjectId
    ) {
        $this->requestData = [];

        $this->wistiaAPIClient = $wistiaAPIClient;

        $this->apiPassword = $apiPassword;

        $this->projectId    = $projectId;
        $this->useProjectId = $useProjectId;
    }

    public function setData(array $data) : WistiaFileUploaderInterface
    {
        $this->requestData = $data;
        return $this;
    }

    public function upload()
    {
        $requestData = $this->prepareRequestData();
        $result = $this->wistiaAPIClient->call(WistiaApiClientInterface::HTTP_METHOD_POST, '', $requestData);

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