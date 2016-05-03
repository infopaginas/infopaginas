<?php

namespace Application\Sonata\AdminBundle\Util\Traits;

use Application\Sonata\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

trait AvailableUserEntityTrait
{
	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="is_active", type="boolean")
	 */
	protected $isActive = true;//:todo This property name have been hardcoded in AppBundle\EventListener\DefaultEntityListener

	/**
	 * @var User
	 *
	 * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User")
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