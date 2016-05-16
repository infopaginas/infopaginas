<?php

namespace Domain\BusinessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
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
class Task
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="type", type="TaskType", nullable=false)
     * @DoctrineAssert\Enum(entity="Domain\BusinessBundle\DBAL\Types\TaskType")
     * @Assert\NotBlank()
     */
    private $type;

    /**
     * @ORM\Column(name="status", type="TaskStatusType", nullable=false)
     * @DoctrineAssert\Enum(entity="Domain\BusinessBundle\DBAL\Types\TaskStatusType")
     * @Assert\NotBlank()
     */
    private $status;

    /**
     * @ORM\Column(name="reject_reason", type="text", nullable=true)
     */
    private $rejectReason;

    /**
     * @ORM\OneToOne(targetEntity="BusinessProfile")
     * @ORM\JoinColumn(name="business_profile_od", referencedColumnName="id", onDelete="SET NULL")
     */
    private $businessProfile;

    /**
     * @ORM\ManyToOne(targetEntity="Oxa\Sonata\UserBundle\Entity\User", inversedBy="tasks")
     * @ORM\JoinColumn(name="reviewer_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $reviewer;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $modifiedAt;

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
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     * @return Task
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * @param mixed $modifiedAt
     * @return Task
     */
    public function setModifiedAt($modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;
        return $this;
    }
}

