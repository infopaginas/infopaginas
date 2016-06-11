<?php

namespace Oxa\WistiaBundle\Manager;

use Oxa\WistiaBundle\Uploader\Model\WistiaFileUploaderInterface;

class WistiaManager
{
    private $wistiaProjectManager;
    private $wistiaMediaManager;

    public function __construct(WistiaProjectManager $projectManager, WistiaMediaManager $mediaManager)
    {
        $this->wistiaProjectManager = $projectManager;
        $this->wistiaMediaManager   = $mediaManager;
    }

    public function listProjects()
    {
        return $this->wistiaProjectManager->list();
    }

    public function showProject(string $hash)
    {
        return $this->wistiaProjectManager->show($hash);
    }

    public function createProject(array $data)
    {
        return $this->wistiaProjectManager->create($data);
    }

    public function updateProject(string $hash, array $data)
    {
        return $this->wistiaProjectManager->update($hash, $data);
    }

    public function removeProject(string $hash)
    {
        return $this->wistiaProjectManager->remove($hash);
    }

    public function copyProject(string $hash)
    {
        return $this->wistiaProjectManager->copy($hash);
    }

    public function listMedia()
    {
        return $this->wistiaMediaManager->list();
    }

    public function showMedia(string $hash)
    {
        return $this->wistiaMediaManager->show($hash);
    }

    public function updateMedia(string $hash, array $data)
    {
        return $this->wistiaMediaManager->update($hash, $data);
    }

    public function removeMedia(string $hash)
    {
        return $this->wistiaMediaManager->remove($hash);
    }

    public function copyMedia(string $hash)
    {
        return $this->wistiaMediaManager->copy($hash);
    }

    public function statsMedia(string $hash)
    {
        return $this->wistiaMediaManager->stats($hash);
    }

    public function upload(WistiaFileUploaderInterface $uploader)
    {
        return $uploader->upload();
    }
}
