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
    public function __construct(
        WistiaApiClientInterface $wistiaAPIClient,
        string $apiPassword,
        int $projectId,
        bool $useProjectId
    ) {
        parent::__construct($wistiaAPIClient, $apiPassword, $projectId, $useProjectId);
    }

    protected function prepareRequestData() : array
    {
        if (!isset($this->requestData['url'])) {
            throw new URLNotProvidedException();
        }

        $url = $this->requestData['url'];

        $name        = $this->requestData['name'] ?? '';
        $description = $this->requestData['description'] ?? '';

        $uploadData = [
            'form_params'    => [
                'api_password' => $this->getApiPassword(),
                'url'          => $url,
                'name'         => $name,
                'description'  => $description,
            ]
        ];

        if (isset($this->requestData['contact_id'])) {
            $uploadData['form_params']['contact_id'] = $this->requestData['contact_id'];
        }

        if (isset($this->requestData['project_id'])) {
            $uploadData['form_params']['project_id'] = $this->requestData['project_id'];
        } else {
            $project = $this->getProjectId();

            if ($project !== 0) {
                $uploadData['form_params']['project_id'] = $project;
            }
        }

        return $uploadData;
    }
}
