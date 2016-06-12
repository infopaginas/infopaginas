<?php
/**
 * Created by PhpStorm.
 * User: Xedin
 * Date: 11.06.16
 * Time: 22:36
 */

namespace Oxa\WistiaBundle\Uploader;

use Oxa\WistiaBundle\Exception\URLNotProvidedException;
use Oxa\WistiaBundle\Service\Model\WistiaApiClientInterface;
use Oxa\WistiaBundle\Uploader\Model\WistiaFileUploaderInterface;

class WistiaRemoteFileUploader extends WistiaFileUploader implements WistiaFileUploaderInterface
{
    public function __construct(WistiaApiClientInterface $wistiaAPIClient)
    {
        parent::__construct($wistiaAPIClient);
    }

    protected function prepareRequestData() : array
    {
        if (!isset($this->requestData['url'])) {
            throw new URLNotProvidedException();
        }

        $url = $this->requestData['url'];

        $name        = $this->requestData['name'] ?? '';
        $description = $this->requestData['description'] ?? '';
        $project     = $this->requestData['project_id'] ?? 2394117; //todo implement project id getter

        $uploadData = [
            'form_params'    => [
                'api_password' => WistiaApiClientInterface::API_PASSWORD,
                'url'          => $url,
                'name'         => $name,
                'project_id'   => $project,
                'description'  => $description,
            ]
        ];

        if (isset($this->requestData['contact_id'])) {
            $uploadData['form_params']['contact_id'] = $this->requestData['contact_id'];
        }

        return $uploadData;
    }
}
