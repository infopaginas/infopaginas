<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 22.06.16
 * Time: 10:05
 */

namespace Oxa\Sonata\UserBundle\Manager;

use Doctrine\ORM\EntityRepository;
use Oxa\Sonata\UserBundle\Entity\Group;

/**
 * Class GroupsManager
 * @package Oxa\Sonata\UserBundle\Manager
 */
class GroupsManager
{
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
            throw new \Exception("Unable to find group by passed code");
        }

        return $group;
    }
}
