<?php

namespace Oxa\WistiaBundle\Manager;

use Oxa\WistiaBundle\Entity\WistiaMedia;
use Oxa\WistiaBundle\Uploader\WistiaLocalFileUploader;
use Oxa\WistiaBundle\Uploader\WistiaRemoteFileUploader;

class WistiaManager
{
    private $wistiaProjectAPIManager;
    private $wistiaMediaAPIManager;
    private $wistiaLocalFileUploader;
    private $wistiaRemoteFileUploader;

    private $wistiaMediaManager;

    public function __construct(
        WistiaProjectAPIManager $projectManager,
        WistiaMediaAPIManager $mediaManager,
        WistiaLocalFileUploader $localFileUploader,
        WistiaRemoteFileUploader $remoteFileUploader,
        wistiaMediaManager $wistiaMediaManager
    ) {
        $this->wistiaProjectAPIManager     = $projectManager;
        $this->wistiaMediaAPIManager       = $mediaManager;
        $this->wistiaLocalFileUploader     = $localFileUploader;
        $this->wistiaRemoteFileUploader    = $remoteFileUploader;

        $this->wistiaMediaManager = $wistiaMediaManager;
    }

    public function listProjects() : array
    {
        return $this->wistiaProjectAPIManager->list();
    }

    public function showProject(string $hash) : array
    {
        return $this->wistiaProjectAPIManager->show($hash);
    }

    public function createProject(array $data) : array
    {
        return $this->wistiaProjectAPIManager->create($data);
    }

    public function updateProject(string $hash, array $data) : array
    {
        return $this->wistiaProjectAPIManager->update($hash, $data);
    }

    public function removeProject(string $hash) : array
    {
        return $this->wistiaProjectAPIManager->remove($hash);
    }

    public function copyProject(string $hash) : array
    {
        return $this->wistiaProjectAPIManager->copy($hash);
    }

    public function listMedia() : array
    {
        return $this->wistiaMediaAPIManager->list();
    }

    public function showMedia(string $hash) : array
    {
        return $this->wistiaMediaAPIManager->show($hash);
    }

    public function updateMedia(string $hash, array $data) : array
    {
        return $this->wistiaMediaAPIManager->update($hash, $data);
    }

    public function removeMedia(string $hash) : array
    {
        return $this->wistiaMediaAPIManager->remove($hash);
    }

    public function copyMedia(string $hash) : array
    {
        return $this->wistiaMediaAPIManager->copy($hash);
    }

    public function statsMedia(string $hash) : array
    {
        return $this->wistiaMediaAPIManager->stats($hash);
    }

    public function uploadLocalFile(string $filePath, array $data = []) : WistiaMedia
    {
        $uploaderRequestData = ['file' => $filePath];
        $fileUploader = $this->wistiaLocalFileUploader->setData(array_merge($uploaderRequestData, $data));

        $wistiaMediaData = $fileUploader->upload();

        return $this->wistiaMediaManager->save($wistiaMediaData);
    }

    public function uploadRemoteFile(string $url, array $data = []) : WistiaMedia
    {
        $uploaderRequestData = ['url' => $url];
        $fileUploader = $this->wistiaRemoteFileUploader->setData(array_merge($uploaderRequestData, $data));

        $wistiaMediaData = $fileUploader->upload();

        return $this->wistiaMediaManager->save($wistiaMediaData);
    }
}
