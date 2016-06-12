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
        if (!isset($this->requestData['file'])) {
            throw new FileNotProvidedException();
        }

        $path = $this->requestData['file'];

        $name        = $this->requestData['name'] ?? '';
        $description = $this->requestData['description'] ?? '';

        $uploadData = [
            'multipart'    => [
                [
                    'name'     => 'file',
                    'contents' => fopen($path, 'r'),
                ],
                [
                    'name'     => 'api_password',
                    'contents' => $this->getApiPassword(),
                ],
                [
                    'name'     => 'name',
                    'contents' => $name,
                ],
                [
                    'name'     => 'description',
                    'contents' => $description,
                ],
            ]
        ];

        if (isset($this->requestData['project_id'])) {
            $projectData = ['name' => 'project_id', 'contents' => $this->requestData['project_id']];
            array_push($uploadData['multipart'], $projectData);
        } else {
            $project = $this->getProjectId();

            if ($project !== 0) {
                $projectData = ['name' => 'project_id', 'contents' => $project];
                array_push($uploadData['multipart'], $projectData);
            }
        }

        return $uploadData;
    }
}
