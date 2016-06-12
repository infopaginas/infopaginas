<?php
/**
 * Created by PhpStorm.
 * User: Xedin
 * Date: 11.06.16
 * Time: 22:36
 */

namespace Oxa\WistiaBundle\Uploader;

use Oxa\WistiaBundle\Exception\FileNotProvidedException;
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
        if (!isset($this->requestData['file'])) {
            throw new FileNotProvidedException();
        }

        $path = $this->requestData['file'];

        $name        = $this->requestData['name'] ?? '';
        $description = $this->requestData['description'] ?? '';
        $project     = $this->requestData['project_id'] ?? 2394117; //todo implement project id getter

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
                    'name'     => 'name',
                    'contents' => $name,
                ],
                [
                    'name'     => 'description',
                    'contents' => $description,
                ],
                [
                    'name'     => 'project_id',
                    'contents' => $project,
                ],
            ]
        ];

        return $uploadData;
    }
}
