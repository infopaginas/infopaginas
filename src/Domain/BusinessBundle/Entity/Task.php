<?php

namespace Domain\BusinessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Domain\BusinessBundle\Model\Task\TaskInterface;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;
use Gedmo\Mapping\Annotation as Gedmo;
use Oxa\Sonata\UserBundle\Entity\User as User;
use Domain\BusinessBundle\DBAL\Types\TaskType;
use Domain\BusinessBundle\DBAL\Types\TaskStatusType;

/**
 * Task
 *
 * @ORM\Table(name="task")
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\TaskRepository")
 */
class Task implements DefaultEntityInterface, TaskInterface
{
    use DefaultEntityTrait;
    
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="type", type="TaskType", nullable=false)
     * @DoctrineAssert\Enum(entity="Domain\BusinessBundle\DBAL\Types\TaskType")
     * @Assert\NotBlank()
     */
    protected $type;

    /**
     * @ORM\Column(name="status", type="TaskStatusType", nullable=false)
     * @DoctrineAssert\Enum(entity="Domain\BusinessBundle\DBAL\Types\TaskStatusType")
     * @Assert\NotBlank()
     */
    protected $status;

    /**
     * @ORM\Column(name="reject_reason", type="text", nullable=true)
     */
    protected $rejectReason;

    /**
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\BusinessProfile", inversedBy="tasks")
     * @ORM\JoinColumn(name="business_review_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $businessProfile;

    /**
     * @ORM\ManyToOne(targetEntity="Oxa\Sonata\UserBundle\Entity\User", inversedBy="tasks")
     * @ORM\JoinColumn(name="reviewer_id", referencedColumnName="id")
     */
    protected $reviewer;

    /**
     * @ORM\OneToOne(targetEntity="Domain\BusinessBundle\Entity\Review\BusinessReview")
     * @ORM\JoinColumn(name="review_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $review;

    /**
     * Task constructor.
     * By default task should be marked as "OPEN"
     *
     * @access public
     */
    public function __construct()
    {
        $this->status = TaskStatusType::TASK_STATUS_OPEN;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     * @return Task
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     * @return Task
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRejectReason()
    {
        return $this->rejectReason;
    }

    /**
     * @param mixed $rejectReason
     * @return Task
     */
    public function setRejectReason($rejectReason)
    {
        $this->rejectReason = $rejectReason;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBusinessProfile()
    {
        return $this->businessProfile;
    }

    /**
     * @param mixed $businessProfile
     * @return Task
     */
    public function setBusinessProfile($businessProfile)
    {
        $this->businessProfile = $businessProfile;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getReviewer()
    {
        return $this->reviewer;
    }

    /**
     * @param mixed $reviewer
     * @return Task
     */
    public function setReviewer($reviewer)
    {
        $this->reviewer = $reviewer;
        return $this;
    }

    /**
     * @return mixed
     */
    public static function getTypes()
    {
        return TaskType::getChoices();
    }

    /**
     * @return mixed
     */
    public static function getStatuses()
    {
        return TaskStatusType::getChoices();
    }

    /**
     * @return mixed
     */
    public function getStatusName()
    {
        $statuses = $this->getStatuses();
        return $statuses[$this->getStatus()];
    }

    /**
     * @return mixed
     */
    public function getTypeName()
    {
        $types = $this->getTypes();
        return $types[$this->getType()];
    }

    /**
     * @return mixed
     */
    public function getReview()
    {
        return $this->review;
    }

    /**
     * @param mixed $review
     * @return Task
     */
    public function setReview($review)
    {
        $this->review = $review;
        return $this;
    }
}
