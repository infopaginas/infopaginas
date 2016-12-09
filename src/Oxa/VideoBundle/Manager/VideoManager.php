<?php

namespace Oxa\VideoBundle\Manager;

use Domain\SiteBundle\Utils\Helpers\SiteHelper;
use Oxa\VideoBundle\Entity\VideoMedia;
use Oxa\VideoBundle\Uploader\VideoLocalFileUploader;
use Oxa\VideoBundle\Uploader\VideoRemoteFileUploader;

class VideoManager
{
    private $videoProjectAPIManager;
    private $videoMediaAPIManager;
    private $videoLocalFileUploader;
    private $videoRemoteFileUploader;

    private $videoMediaManager;

    private $videoEmbedAPIManager;

    public function __construct(
        VideoProjectAPIManager $projectManager,
        VideoMediaAPIManager $mediaManager,
        VideoLocalFileUploader $localFileUploader,
        VideoRemoteFileUploader $remoteFileUploader,
        VideoMediaManager $videoMediaManager,
        VideoEmbedAPIManager $videoEmbedAPIManager
    ) {
        $this->videoProjectAPIManager     = $projectManager;
        $this->videoMediaAPIManager       = $mediaManager;
        $this->videoLocalFileUploader     = $localFileUploader;
        $this->videoRemoteFileUploader    = $remoteFileUploader;

        $this->videoMediaManager = $videoMediaManager;

        $this->videoEmbedAPIManager = $videoEmbedAPIManager;
    }

    public function listProjects() : array
    {
        return $this->videoProjectAPIManager->list();
    }

    public function showProject(string $hash) : array
    {
        return $this->videoProjectAPIManager->show($hash);
    }

    public function createProject(array $data) : array
    {
        return $this->videoProjectAPIManager->create($data);
    }

    public function updateProject(string $hash, array $data) : array
    {
        return $this->videoProjectAPIManager->update($hash, $data);
    }

    public function removeProject(string $hash) : array
    {
        return $this->videoProjectAPIManager->remove($hash);
    }

    public function copyProject(string $hash) : array
    {
        return $this->videoProjectAPIManager->copy($hash);
    }

    public function listMedia() : array
    {
        return $this->videoMediaAPIManager->list();
    }

    public function showMedia(string $hash) : array
    {
        return $this->videoMediaAPIManager->show($hash);
    }

    public function updateMedia(string $hash, array $data) : array
    {
        return $this->videoMediaAPIManager->update($hash, $data);
    }

    public function removeMedia(string $hash) : array
    {
        return $this->videoMediaAPIManager->remove($hash);
    }

    public function copyMedia(string $hash) : array
    {
        return $this->videoMediaAPIManager->copy($hash);
    }

    public function statsMedia(string $hash) : array
    {
        return $this->videoMediaAPIManager->stats($hash);
    }

    public function uploadLocalFile(string $filePath, array $data = []) : VideoMedia
    {
        $videoMediaData = $this->uploadLocalFileData($filePath, $data);

        return $this->videoMediaManager->save($videoMediaData);
    }

    public function uploadLocalFileData(string $filePath, array $data = []) : array
    {
        $uploaderRequestData = ['file' => $filePath];
        $fileUploader = $this->videoLocalFileUploader->setData(array_merge($uploaderRequestData, $data));

        $videoMediaData = $fileUploader->upload();

        return $videoMediaData;
    }

    public function uploadRemoteFile(string $url, array $data = [])
    {
        $headers = SiteHelper::checkUrlExistence($url);

        if ($headers && in_array($headers['content_type'], SiteHelper::$videoContentTypes)) {
            $uploaderRequestData = ['url' => $url];
            $fileUploader = $this->videoRemoteFileUploader->setData(array_merge($uploaderRequestData, $data));

            $videoMediaData = $fileUploader->upload();

            return $this->videoMediaManager->save($videoMediaData);
        }

        return false;
    }

    public function getEmbedCode(string $hash, array $dimensions = [])
    {
        return $this->videoEmbedAPIManager->get($hash, $dimensions);
    }
}
