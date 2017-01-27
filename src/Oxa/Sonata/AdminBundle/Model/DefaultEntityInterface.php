<?php

namespace Oxa\Sonata\AdminBundle\Model;

use Oxa\Sonata\UserBundle\Entity\User;

/**
 * Should be implemented to all entities for extended CRUD functional
 *
 * Interface DefaultEntityInterface
 * @package Oxa\Sonata\AdminBundle\Model
 */
interface DefaultEntityInterface
{
    const IS_ACTIVE_PROPERTY_NAME = 'isActive';
    const CREATE_USER_PROPERTY_NAME = 'createdUser';
    const UPDATE_USER_PROPERTY_NAME = 'updatedUser';

    /**
     * Sets createdAt.
     *
     * @param  \DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt);

    /**
     * Returns createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @param User $user
     * @return $this
     */
    public function setCreatedUser(User $user);

    /**
     * @return User
     */
    public function getCreatedUser();

    /**
     * Sets updatedAt.
     *
     * @param  \DateTime $updatedAt
     * @return $this
     */
    public function setUpdatedAt(\DateTime $updatedAt);

    /**
     * Returns updatedAt.
     *
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * @param User $user
     * @return $this
     */
    public function setUpdatedUser(User $user);

    /**
     * @return User
     */
    public function getUpdatedUser();

    /**
     * @param boolean $isActive
     * @return $this
     */
    public function setIsActive($isActive);

    /**
     * @return bool
     */
    public function getIsActive();

    /**
     * @param User $user
     * @return $this
     */
    public function setIsActiveUser(User $user);

    /**
     * @return User
     */
    public function getIsActiveUser();
}
