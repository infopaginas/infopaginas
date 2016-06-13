<?php

namespace Oxa\ManagerArchitectureBundle\Model\Manager;

use Doctrine\ORM\EntityManager;

abstract class Manager
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * Manager constructor.
     * Accepts only entityManager as main dependency.
     * Regargless hole container, need to keep it clear and work only with needed dependency
     *
     * @access public
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function getRepository()
    {
        return $this->em->getRepository(
            $this->buildRepositoryPath()
        );
    }

    protected function getEntityName()
    {
        $namespaceParts = $this->getNamespaceParts();
        $entityName = preg_replace('%Manager%', '', end($namespaceParts));

        return $entityName;
    }

    protected function getEntityPath()
    {
        $namespaceParts = $this->getNamespaceParts();
        return $namespaceParts[0] . $namespaceParts[1];
    }

    protected function buildRepositoryPath()
    {
        return $this->getEntityPath() . ':' . $this->getEntityName();
    }

    protected function getNamespaceParts()
    {
        return explode('\\', get_called_class());
    }
}
