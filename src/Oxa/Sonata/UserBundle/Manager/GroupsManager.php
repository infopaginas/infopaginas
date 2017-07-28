<?php

namespace Oxa\Sonata\UserBundle\Manager;

use Doctrine\ORM\EntityRepository;
use Oxa\Sonata\UserBundle\Entity\Group;

/**
 * Class GroupsManager
 * @package Oxa\Sonata\UserBundle\Manager
 */
class GroupsManager
{
    const ERROR_NO_GROUP_FOUND_MESSAGE = 'Unable to find group by passed code';

    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * GroupsManager constructor.
     * @param EntityRepository $repository
     */
    public function __construct(EntityRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param string $code
     * @return Group
     * @throws \Exception
     */
    public function findByCode(string $code) : Group
    {
        $group = $this->repository->findOneBy(['code' => $code]);

        if ($group === null) {
            throw new \Exception(self::ERROR_NO_GROUP_FOUND_MESSAGE);
        }

        return $group;
    }
}
