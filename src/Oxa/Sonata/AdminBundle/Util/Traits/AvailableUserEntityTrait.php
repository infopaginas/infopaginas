<?php

namespace Oxa\Sonata\AdminBundle\Util\Traits;

use Oxa\Sonata\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Set user who made an object active or not
 *
 * Class AvailableUserEntityTrait
 * @package Oxa\Sonata\AdminBundle\Util\Traits
 */
trait AvailableUserEntityTrait
{
    /**
     * Property must be named as DefaultEntityInterface::IS_ACTIVE_PROPERTY_NAME, used in UserCRUDActionListener
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", options={"default" : 0})
     */
    protected $isActive = true;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Oxa\Sonata\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="is_active_user_id")
     */
    protected $isActiveUser;

    /**
     * @param boolean $isActive
     * @return $this
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function setIsActiveUser(User $user)
    {
        $this->isActiveUser = $user;

        return $this;
    }

    /**
     * @return User
     */
    public function getIsActiveUser()
    {
        return $this->isActiveUser;
    }
}
