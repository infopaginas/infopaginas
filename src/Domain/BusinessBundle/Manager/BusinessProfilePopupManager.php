<?php

namespace Domain\BusinessBundle\Manager;

use Oxa\ManagerArchitectureBundle\Model\Manager\FileUploadManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BusinessProfilePopupManager extends FileUploadManager
{
    /**
     * Manager constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    protected function getCdnPath(): string
    {
        return sprintf(
            '%s/%s',
            $this->container->getParameter('amazon_aws_base_host'),
            $this->container->getParameter('amazon_aws_business_popup_directory')
        );
    }
}
