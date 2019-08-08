<?php

namespace Domain\BusinessBundle\Manager;

use Doctrine\ORM\EntityManager;
use Domain\BusinessBundle\Entity\CSVImportFile;
use Gaufrette\Filesystem;
use Oxa\ManagerArchitectureBundle\Model\Manager\Manager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CSVImportFileManager extends Manager
{
    const FILE_TYPE = 'text/csv';

    /** @var  ContainerInterface $container */
    protected $container;

    /** @var  Filesystem $filesystem */
    protected $filesystem;

    /**
     * @param ContainerInterface $container
     * @param EntityManager $entityManager
     */
    public function __construct(ContainerInterface $container, EntityManager $entityManager)
    {
        $this->container  = $container;
        $this->filesystem = $this->container->get('mass_import_storage_filesystem');
        parent::__construct($entityManager);
    }

    public function upload(CSVImportFile $csvImportFile)
    {
        $adapter = $this->filesystem->getAdapter();
        $tempFilePath = $csvImportFile->getFile();

        $path = sprintf('%s/%s/', date('Y'), date('m'));
        do {
            $filename = sprintf('%s.%s', uniqid('', true), 'csv');
        } while ($adapter->exists($path . $filename));

        $adapter->setMetadata($path . $filename, [
            'contentType'   => self::FILE_TYPE,
            'ACL'           => 'public-read',
        ]);

        $uploadedSize = $adapter->write($path . $filename, file_get_contents($tempFilePath));

        return [
            'status' => (bool) $uploadedSize,
            'link'   => $this->getPublicUrl($path, $filename),
        ];
    }

    /**
     * @param string $path
     * @param string $filename
     *
     * @return string
     */
    public function getPublicUrl($path, $filename)
    {
        $url = sprintf(
            '%s/%s%s',
            $this->getCdnPath(),
            $path,
            $filename
        );

        return $url;
    }

    /**
     * @return string
     */
    protected function getCdnPath()
    {
        $path = sprintf(
            '%s/%s',
            $this->container->getParameter('amazon_aws_base_host'),
            $this->container->getParameter('amazon_aws_mass_import_directory')
        );

        return $path;
    }
}
