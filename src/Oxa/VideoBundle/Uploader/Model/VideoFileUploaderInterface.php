<?php

namespace Oxa\VideoBundle\Uploader\Model;

use Oxa\VideoBundle\Service\Model\VideoApiClientInterface;
use Oxa\VideoBundle\Service\VideoAPIClient;

/**
 * Created by PhpStorm.
 * User: Xedin
 * Date: 11.06.16
 * Time: 22:35
 */
interface VideoFileUploaderInterface
{
    public function __construct(
        VideoApiClientInterface $videoAPIClient,
        string $apiPassword,
        int $projectId,
        bool $useProjectId
    );

    public function setData(array $data);

    public function upload();
}
