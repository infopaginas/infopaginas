<?php

namespace Oxa\ManagerArchitectureBundle\Model\Manager;

use Gaufrette\Filesystem;
use Oxa\Sonata\AdminBundle\Model\FileUploadEntityInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

abstract class FileUploadManager extends Manager
{
    use ContainerAwareTrait;

    /** @var  Filesystem $filesystem */
    protected $filesystem;

    public function setFileSystem(Filesystem $fs): void
    {
        $this->filesystem = $fs;
    }

    public function upload(FileUploadEntityInterface $entity): array
    {
        $adapter = $this->filesystem->getAdapter();
        $tempFilePath = $entity->getFile();

        $path = sprintf('%s/%s/', date('Y'), date('m'));
        do {
            $filename = sprintf('%s.%s', uniqid('', true), $entity->getFileExtension());
        } while ($adapter->exists($path . $filename));

        $adapter->setMetadata($path . $filename, [
            'contentType' => $entity->getFileMimeType(),
            'ACL'         => 'public-read',
        ]);

        $uploadedSize = $adapter->write($path . $filename, file_get_contents($tempFilePath));

        return [
            'status' => (bool) $uploadedSize,
            'link'   => $this->getPublicUrl($path, $filename),
        ];
    }

    protected function getPublicUrl(string $path, string $filename): string
    {
        return sprintf(
            '%s/%s%s',
            $this->getCdnPath(),
            $path,
            $filename
        );
    }

    abstract protected function getCdnPath(): string;
}
