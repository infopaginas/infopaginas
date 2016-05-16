<?php

namespace Oxa\Sonata\UserBundle\Entity;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Oxa\Sonata\AdminBundle\Model\CopyableEntityInterface;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\AvailableUserEntityTrait;
use Oxa\Sonata\AdminBundle\Util\Traits\DeleteableUserEntityTrait;
use Oxa\Sonata\AdminBundle\Util\Traits\UserCUableEntityTrait;
use Doctrine\ORM\Mapping as ORM;
use Oxa\Sonata\UserBundle\Model\UserRoleInterface;
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
class User extends BaseUser implements DefaultEntityInterface, UserRoleInterface
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
     * @ORM\OneToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\BusinessProfile", 
     *     mappedBy="user", 
     *     cascade={"persist", "remove"}, 
     *     orphanRemoval=true
     *     )
     */
    protected $businessProfiles;

    /**
     * Set role with group permissions
     *
     * @param Group $role
     *
     * @return User
     */
    public function setRole(Group $role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return Group
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Add role groups to user to apply user roles
     * when user role has been changed
     *
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function checkRole(LifecycleEventArgs $args)
    {
        $changedFields = $args->getEntityManager()
            ->getUnitOfWork()
            ->getEntityChangeSet($args->getEntity());

        if (array_key_exists(UserRoleInterface::ROLE_PROPERTY_NAME, $changedFields)) {

            $this->updateRoleGroup();

            // persist changes
            $args->getEntityManager()->persist($this);
            $args->getEntityManager()->flush();
        }
    }

    /**
     * Add role group to apply real roles
     *
     * @return $this
     */
    public function updateRoleGroup()
    {
        // remove previous groups
        foreach ($this->getGroups() as $group) {
            $this->removeGroup($group);
        }

        // add needed group to apply group roles
        $this->addGroup($this->getRole());
        
        return $this;
    }
}
