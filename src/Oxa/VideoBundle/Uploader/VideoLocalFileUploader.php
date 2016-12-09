<?php
/**
 * Created by PhpStorm.
 * User: Xedin
 * Date: 11.06.16
 * Time: 22:36
 */

namespace Oxa\VideoBundle\Uploader;

use Oxa\VideoBundle\Exception\FileNotProvidedException;
use Oxa\VideoBundle\Service\Model\VideoApiClientInterface;
use Oxa\VideoBundle\Uploader\Model\VideoFileUploaderInterface;

class VideoLocalFileUploader extends VideoFileUploader implements VideoFileUploaderInterface
{
    public function __construct(
        VideoApiClientInterface $videoAPIClient,
        string $apiPassword,
        int $projectId,
        bool $useProjectId
    ) {
        parent::__construct($videoAPIClient, $apiPassword, $projectId, $useProjectId);
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
