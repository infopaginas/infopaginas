<?php
/**
 * Created by PhpStorm.
 * User: Xedin
 * Date: 11.06.16
 * Time: 22:36
 */

namespace Oxa\WistiaBundle\Uploader;

use Oxa\WistiaBundle\Service\Model\WistiaApiClientInterface;
use Oxa\WistiaBundle\Uploader\Model\WistiaFileUploaderInterface;

class WistiaLocalFileUploader extends WistiaFileUploader implements WistiaFileUploaderInterface
{
    public function __construct(WistiaApiClientInterface $wistiaAPIClient)
    {
        parent::__construct($wistiaAPIClient);
    }

    protected function prepareRequestData() : array
    {
        //todo: exception if file not provided
        $path = $this->requestData['file'];

        $uploadData = [
            'multipart'    => [
                [
                    'name'     => 'file',
                    'contents' => fopen($path, 'r'),
                ],
                [
                    'name'     => 'api_password',
                    'contents' => WistiaApiClientInterface::API_PASSWORD,
                ],
                [
                    'name'     => 'project_id',
                    'contents' => 2394117 //todo: get project id
                ],
            ]
        ];

        return $uploadData;
    }
}