<?php

namespace Application\Sonata\AdminBundle\Util\Traits;

use Application\Sonata\UserBundle\Entity\User;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

trait DeleteableUserEntityTrait
{
	use SoftDeleteableEntity;

	/**
	 * @var User
	 *
	 * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User")
	 * @ORM\JoinColumn(name="deleted_user_id")
	 */
	protected $deletedUser;//:todo This property name have been hardcoded in AppBundle\EventListener\DefaultEntityListener

	/**
	 * @param User $user
	 * @return $this
	 */
	public function setDeletedUser(User $user)
	{
		$this->deletedUser = $user;

		return $this;
	}

	/**
	 * @return User
	 */
	public function getDeletedUser()
	{
		return $this->deletedUser;
	}
}