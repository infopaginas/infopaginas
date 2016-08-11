<?php

namespace Domain\BusinessBundle\Util\Traits;

use Domain\BusinessBundle\Model\StatusInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Exception\InvalidArgumentException;
use Oxa\Sonata\AdminBundle\Util\Traits\DatetimePeriodTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class VideoUploadTrait
 * @package Domain\BusinessBundle\Util\Traits
 */
trait VideoUploadTrait
{
    private function uploadVideoToLocalServer(array $files)
    {
        /** @var UploadedFile $video */
        $video = $files[0];

        $videosUploadPath = $this->getMediaUploadDirectory();

        $filename = $video->getClientOriginalName();

        $video->move($videosUploadPath, $filename);

        $videoPathOnLocalServer = $videosUploadPath . DIRECTORY_SEPARATOR . $filename;

        return [$videoPathOnLocalServer, $filename];
    }

    private function getMediaUploadDirectory()
    {
        return $this->getParameter('videos_upload_path');
    }
}
