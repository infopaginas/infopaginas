<?php

namespace Oxa\Sonata\UserBundle\Entity;

use Oxa\Sonata\AdminBundle\Model\CopyableEntityInterface;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\AvailableUserEntityTrait;
use Oxa\Sonata\AdminBundle\Util\Traits\DeleteableUserEntityTrait;
use Oxa\Sonata\AdminBundle\Util\Traits\UserCUableEntityTrait;
use Doctrine\ORM\Mapping as ORM;
use Sonata\UserBundle\Entity\BaseUser as BaseUser;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="fos_user_user")
 * @ORM\Entity(repositoryClass="Oxa\Sonata\UserBundle\Entity\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @UniqueEntity("username")
 * */
class User extends BaseUser implements DefaultEntityInterface
{
    use AvailableUserEntityTrait, DeleteableUserEntityTrait, UserCUableEntityTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Group
     *
     * @ORM\ManyToOne(targetEntity="Oxa\Sonata\UserBundle\Entity\Group", inversedBy="role_users")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", nullable=false)
     */
    protected $role;

    /**
     * Set role with group permissions
     *
     * @param \Oxa\Sonata\UserBundle\Entity\Group $role
     *
     * @return User
     */
    public function setRole(\Oxa\Sonata\UserBundle\Entity\Group $role)
    {
        $this->role = $role;

        // remove previous groups and add needed new one to apply group roles
        foreach ($this->getGroups() as $group)
        {
            $this->removeGroup($group);
        }

        $this->addGroup($role);

        return $this;
    }

    /**
     * Get role
     *
     * @return \Oxa\Sonata\UserBundle\Entity\Group
     */
    public function getRole()
    {
        return $this->role;
    }
}
