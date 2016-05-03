<?php

namespace Application\Sonata\AdminBundle\Util\Traits;

use Application\Sonata\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Set user who created or updated an object
 * 
 * Class UserCUableEntityTrait
 * @package Application\Sonata\AdminBundle\Util\Traits
 */
trait UserCUableEntityTrait
{
	/**
	 * @var User
	 *
	 * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", cascade={"persist"})
	 * @ORM\JoinColumn(name="created_user_id")
	 */
	protected $createdUser;

	/**
	 * @var User
	 *
	 * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", cascade={"persist"})
	 * @ORM\JoinColumn(name="updated_user_id")
	 */
	protected $updatedUser;

	/**
	 * @param User $user
	 * @return $this
	 */
	public function setCreatedUser(User $user)
	{
		$this->createdUser = $user;

		return $this;
	}

	/**
	 * @return User
	 */
	public function getCreatedUser()
	{
		return $this->createdUser;
	}

	/**
	 * @param User $user
	 * @return $this
	 */
	public function setUpdatedUser(User $user)
	{
		$this->updatedUser = $user;

		return $this;
	}

	/**
	 * @return User
	 */
	public function getUpdatedUser()
	{
		return $this->updatedUser;
	}
}