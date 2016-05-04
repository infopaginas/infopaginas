<?php

namespace Application\Sonata\AdminBundle\Util\Traits;

use Application\Sonata\UserBundle\Entity\User;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

trait DeleteableUserEntityTrait
{
    use SoftDeleteableEntity;

    /**
     * Property must be named as DeleteableEntityInterface::DELETED_USER_PROPERTY_NAME, used in UserCRUDActionListener
     *
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="deleted_user_id")
     */
    protected $deletedUser;

    /**
     * @return User
     */
    public function getDeletedUser()
    {
        return $this->deletedUser;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function setDeletedUser(User $user)
    {
        $this->deletedUser = $user;

        return $this;
    }
}
