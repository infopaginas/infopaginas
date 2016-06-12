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

    public function __construct(WistiaApiClientInterface $wistiaAPIClient)
    {
        $this->wistiaAPIClient = $wistiaAPIClient;
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

    abstract protected function prepareRequestData() : array;
}