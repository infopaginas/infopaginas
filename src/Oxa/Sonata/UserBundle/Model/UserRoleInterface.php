<?php

namespace Oxa\Sonata\UserBundle\Model;

use Oxa\Sonata\UserBundle\Entity\Group;

/**
 * Add role group when new role has been added
 *
 * Interface UserRoleInterface
 * @package Oxa\Sonata\AdminBundle\Model
 */
interface UserRoleInterface
{
    const ROLE_PROPERTY_NAME = 'role';

    /**
     * Get role
     *
     * @return Group
     */
    public function getRole();

    /**
     * @param Group $role
     * @return $this
     */
    public function setRole(Group $role);

    /**
     * Add role group to apply real roles
     *
     * @return $this
     */
    public function updateRoleGroup();
}
