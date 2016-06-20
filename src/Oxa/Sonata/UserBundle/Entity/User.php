<?php

namespace Oxa\Sonata\UserBundle\Entity;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\AvailableUserEntityTrait;
use Oxa\Sonata\AdminBundle\Util\Traits\DeleteableUserEntityTrait;
use Oxa\Sonata\AdminBundle\Util\Traits\UserCUableEntityTrait;
use Doctrine\ORM\Mapping as ORM;
use Oxa\Sonata\UserBundle\Model\UserRoleInterface;
use Sonata\UserBundle\Entity\BaseUser as BaseUser;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Domain\BusinessBundle\Entity\Task\Task;

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
     * @ORM\ManyToOne(targetEntity="Oxa\Sonata\UserBundle\Entity\Group", inversedBy="roleUsers")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", nullable=false)
     */
    protected $role;

    /**
     * @var BusinessProfile[]
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\BusinessProfile",
     *     mappedBy="user",
     *     cascade={"persist", "remove"}
     *     )
     */
    protected $businessProfiles;

    /**
     * @var Task[]
     *
     * @ORM\OneToMany(targetEntity="Domain\BusinessBundle\Entity\Task\Task", mappedBy="reviewer")
     */
    protected $tasks;

    /**
     * @var BusinessProfile[]
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\Review\BusinessReview",
     *     mappedBy="user",
     *     cascade={"persist", "remove"}
     *     )
     */
    protected $businessReviews;

    /**
     * @ORM\Column(name="facebook_id", type="string", length=255, nullable=true)
     */
    private $facebookId;

    /**
     * @ORM\Column(name="facebook_access_token", type="string", length=255, nullable=true)
     */
    private $facebookAccessToken;

    /**
     * @ORM\Column(name="google_id", type="string", length=255, nullable=true)
     */
    private $googleId;

    /**
     * @ORM\Column(name="google_access_token", type="string", length=255, nullable=true)
     */
    private $googleAccessToken;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->businessProfiles = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tasks = new \Doctrine\Common\Collections\ArrayCollection();
        $this->businessReviews = new \Doctrine\Common\Collections\ArrayCollection();
    }

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

    /**
     * Add order
     *
     * @param Task $task
     *
     * @return User
     */
    public function addTask(Task $task)
    {
        $this->tasks[] = $task;

        return $this;
    }

    /**
     * Remove order
     *
     * @param Task $task
     */
    public function removeTask(Task $task)
    {
        $this->tasks->removeElement($task);
    }

    /**
     * Get orders
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    /**
     * Add businessProfile
     *
     * @param \Domain\BusinessBundle\Entity\BusinessProfile $businessProfile
     *
     * @return User
     */
    public function addBusinessProfile(\Domain\BusinessBundle\Entity\BusinessProfile $businessProfile)
    {
        $this->businessProfiles[] = $businessProfile;

        return $this;
    }

    /**
     * Remove businessProfile
     *
     * @param \Domain\BusinessBundle\Entity\BusinessProfile $businessProfile
     */
    public function removeBusinessProfile(\Domain\BusinessBundle\Entity\BusinessProfile $businessProfile)
    {
        $this->businessProfiles->removeElement($businessProfile);
    }

    /**
     * Get businessProfiles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBusinessProfiles()
    {
        return $this->businessProfiles;
    }

    /**
     * Add businessReview
     *
     * @param \Domain\BusinessBundle\Entity\Review\BusinessReview $businessReview
     *
     * @return User
     */
    public function addBusinessReview(\Domain\BusinessBundle\Entity\Review\BusinessReview $businessReview)
    {
        $this->businessReviews[] = $businessReview;

        return $this;
    }

    /**
     * Remove businessReview
     *
     * @param \Domain\BusinessBundle\Entity\Review\BusinessReview $businessReview
     */
    public function removeBusinessReview(\Domain\BusinessBundle\Entity\Review\BusinessReview $businessReview)
    {
        $this->businessReviews->removeElement($businessReview);
    }

    /**
     * Get businessReviews
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBusinessReviews()
    {
        return $this->businessReviews;
    }

    /**
     * @param string $facebookId
     * @return User
     */
    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;

        return $this;
    }

    /**
     * @return string
     */
    public function getFacebookId()
    {
        return $this->facebookId;
    }

    /**
     * @param string $facebookAccessToken
     * @return User
     */
    public function setFacebookAccessToken($facebookAccessToken)
    {
        $this->facebookAccessToken = $facebookAccessToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getFacebookAccessToken()
    {
        return $this->facebookAccessToken;
    }

    /**
     * @return mixed
     */
    public function getGoogleId()
    {
        return $this->googleId;
    }

    /**
     * @param mixed $googleId
     * @return User
     */
    public function setGoogleId($googleId)
    {
        $this->googleId = $googleId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getGoogleAccessToken()
    {
        return $this->googleAccessToken;
    }

    /**
     * @param mixed $googleAccessToken
     * @return User
     */
    public function setGoogleAccessToken($googleAccessToken)
    {
        $this->googleAccessToken = $googleAccessToken;
        return $this;
    }
}
