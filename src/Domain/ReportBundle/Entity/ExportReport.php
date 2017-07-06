<?php

namespace Domain\ReportBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Oxa\Sonata\UserBundle\Entity\User;

/**
 * Visitor
 *
 * @ORM\Table(name="export_report")
 * @ORM\Entity(repositoryClass="Domain\ReportBundle\Repository\ExportReportRepository")
 */
class ExportReport
{
    const STATUS_PENDING = 'pending';
    const STATUS_ERROR   = 'error';
    const STATUS_READY   = 'ready';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="type", type="string", length=255)
     */
    protected $type;

    /**
     * @var string
     * @ORM\Column(name="class", type="string", length=255)
     */
    protected $class;

    /**
     * @var string
     * @ORM\Column(name="format", type="string", length=255)
     */
    protected $format;

    /**
     * @var User - Business owner
     * @ORM\ManyToOne(targetEntity="Oxa\Sonata\UserBundle\Entity\User",
     *     inversedBy="exports",
     *     cascade={"persist"}
     *     )
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @var string
     * @ORM\Column(name="status", type="string", length=255)
     */
    protected $status;

    /**
     * @var array
     * @ORM\Column(name="params", type="json_array")
     */
    protected $params;

    /**
     * @var array
     * @ORM\Column(name="links", type="json_array", nullable=true)
     */
    protected $links;

    /**
     * ExportReport constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->status    = self::STATUS_PENDING;
    }

    public function __toString()
    {
        return $this->getType();
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
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return ExportReport
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param string $class
     *
     * @return ExportReport
     */
    public function setClass($class)
    {
        $this->class = $class;

        $entityPath = explode('\\', $class);
        $this->setType(end($entityPath));

        return $this;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return ExportReport
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return ExportReport
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * @return ExportReport
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @param string $format
     * @return ExportReport
     */
    public function setFormat($format)
    {
        $this->format = $format;
        return $this;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param array $params
     *
     * @return ExportReport
     */
    public function setParams($params)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * @return array
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * @param array $links
     *
     * @return ExportReport
     */
    public function setLinks($links)
    {
        $this->links = $links;

        return $this;
    }
}
