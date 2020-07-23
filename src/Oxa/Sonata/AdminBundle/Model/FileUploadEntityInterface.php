<?php

namespace Oxa\Sonata\AdminBundle\Model;

/**
 * Used for uploading files
 *
 * Interface FileUploadEntityInterface
 * @package Oxa\Sonata\AdminBundle\Model
 */
interface FileUploadEntityInterface
{
    /**
     * @return string
     */
    public function getFile();

    public function setFile($file);

    public function getFileExtension(): string;

    public function getFileMimeType(): string;
}
