<?php

namespace Oxa\Sonata\AdminBundle\Model;

use Oxa\Sonata\UserBundle\Entity\User;

/**
 * Used for soft delete
 *
 * Interface DeleteableEntityInterface
 * @package Oxa\Sonata\AdminBundle\Model
 */
interface DeleteableEntityInterface
{
    const DELETED_USER_PROPERTY_NAME = 'deletedUser';

    /**
     * Sets deletedAt.
     *
     * @param \Datetime|null $deletedAt
     *
     * @return $this
     */
    public function setDeletedAt(\DateTime $deletedAt = null);

    /**
     * Returns deletedAt.
     *
     * @return \DateTime
     */
    public function getDeletedAt();

    /**
     * @param User $user
     * @return $this
     */
    public function setDeletedUser(User $user);

    /**
     * @return User
     */
    public function getDeletedUser();
}
