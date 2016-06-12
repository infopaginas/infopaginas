<?php

namespace Oxa\WistiaBundle\Manager;

use Oxa\WistiaBundle\Uploader\Model\WistiaFileUploaderInterface;
use Oxa\WistiaBundle\Uploader\WistiaLocalFileUploader;
use Oxa\WistiaBundle\Uploader\WistiaRemoteFileUploader;

class WistiaManager
{
    private $wistiaProjectManager;
    private $wistiaMediaManager;
    private $wistiaLocalFileUploader;
    private $wistiaRemoteFileUploader;

    public function __construct(
        WistiaProjectManager $projectManager,
        WistiaMediaManager $mediaManager,
        WistiaLocalFileUploader $localFileUploader,
        WistiaRemoteFileUploader $remoteFileUploader
    ) {
        $this->wistiaProjectManager     = $projectManager;
        $this->wistiaMediaManager       = $mediaManager;
        $this->wistiaLocalFileUploader  = $localFileUploader;
        $this->wistiaRemoteFileUploader = $remoteFileUploader;
    }

    public function listProjects() : array
    {
        return $this->wistiaProjectManager->list();
    }

    public function showProject(string $hash) : array
    {
        return $this->wistiaProjectManager->show($hash);
    }

    public function createProject(array $data) : array
    {
        return $this->wistiaProjectManager->create($data);
    }

    public function updateProject(string $hash, array $data) : array
    {
        return $this->wistiaProjectManager->update($hash, $data);
    }

    public function removeProject(string $hash) : array
    {
        return $this->wistiaProjectManager->remove($hash);
    }

    public function copyProject(string $hash) : array
    {
        return $this->wistiaProjectManager->copy($hash);
    }

    public function listMedia() : array
    {
        return $this->wistiaMediaManager->list();
    }

    public function showMedia(string $hash) : array
    {
        return $this->wistiaMediaManager->show($hash);
    }

    public function updateMedia(string $hash, array $data) : array
    {
        return $this->wistiaMediaManager->update($hash, $data);
    }

    public function removeMedia(string $hash) : array
    {
        return $this->wistiaMediaManager->remove($hash);
    }

    public function copyMedia(string $hash) : array
    {
        return $this->wistiaMediaManager->copy($hash);
    }

    public function statsMedia(string $hash) : array
    {
        return $this->wistiaMediaManager->stats($hash);
    }

    public function uploadLocalFile(string $filePath, array $data = []) : array
    {
        $uploaderRequestData = ['file' => $filePath];
        $fileUploader = $this->wistiaLocalFileUploader->setData(array_merge($uploaderRequestData, $data));

        return $fileUploader->upload();
    }

    public function uploadRemoteFile(string $url, array $data = []) : array
    {
        $uploaderRequestData = ['url' => $url];
        $fileUploader = $this->wistiaRemoteFileUploader->setData(array_merge($uploaderRequestData, $data));

        return $fileUploader->upload();
    }
}
