<?php

namespace Oxa\WistiaBundle\Uploader;
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

    public function __construct(WistiaAPIClient $wistiaAPIClient)
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
        // TODO: Implement upload() method.
    }

    abstract protected function prepareRequestData();
}