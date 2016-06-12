<?php

namespace Oxa\WistiaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WistiaMedia
 *
 * @ORM\Table(name="wistia_medias")
 * @ORM\Entity(repositoryClass="Oxa\WistiaBundle\Repository\WistiaMediaRepository")
 */
class WistiaMedia
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
     * @var integer
     *
     * @ORM\Column(name="wistia_id", type="integer")
     */
    private $wistiaId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="hashed_id", type="string", length=255)
     */
    private $hashedId;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var float
     *
     * @ORM\Column(name="progress", type="float")
     */
    private $progress;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    /**
     * @var integer
     *
     * @ORM\Column(name="account_id", type="integer")
     */
    private $accountId;

    /**
     * @ORM\OneToOne(targetEntity="WistiaMediaThumbnail", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="thumbnail_id", referencedColumnName="id")
     */
    private $thumbnail;

    public function __construct(array $wistiaMediaData)
    {
        $this->setWistiaId($wistiaMediaData['id']);
        $this->setName($wistiaMediaData['name']);
        $this->setType($wistiaMediaData['type']);
        $this->setCreatedAt(new \DateTime($wistiaMediaData['created']));
        $this->setUpdatedAt(new \DateTime($wistiaMediaData['updated']));
        $this->setHashedId($wistiaMediaData['hashed_id']);
        $this->setDescription($wistiaMediaData['description']);
        $this->setProgress($wistiaMediaData['progress']);
        $this->setStatus($wistiaMediaData['status']);
        $this->setAccountId($wistiaMediaData['account_id']);

        $thumbnail = new WistiaMediaThumbnail($wistiaMediaData['thumbnail']);
        $this->setThumbnail($thumbnail);

        return $this;
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
     * Set wistiaId
     *
     * @param integer $wistiaId
     *
     * @return WistiaMedia
     */
    public function setWistiaId($wistiaId)
    {
        $this->wistiaId = $wistiaId;

        return $this;
    }

    /**
     * Get wistiaId
     *
     * @return integer
     */
    public function getWistiaId()
    {
        return $this->wistiaId;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return WistiaMedia
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return WistiaMedia
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return WistiaMedia
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return WistiaMedia
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set hashedId
     *
     * @param string $hashedId
     *
     * @return WistiaMedia
     */
    public function setHashedId($hashedId)
    {
        $this->hashedId = $hashedId;

        return $this;
    }

    /**
     * Get hashedId
     *
     * @return string
     */
    public function getHashedId()
    {
        return $this->hashedId;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return WistiaMedia
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set progress
     *
     * @param float $progress
     *
     * @return WistiaMedia
     */
    public function setProgress($progress)
    {
        $this->progress = $progress;

        return $this;
    }

    /**
     * Get progress
     *
     * @return float
     */
    public function getProgress()
    {
        return $this->progress;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return WistiaMedia
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set accountId
     *
     * @param integer $accountId
     *
     * @return WistiaMedia
     */
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;

        return $this;
    }

    /**
     * Get accountId
     *
     * @return integer
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * Set thumbnail
     *
     * @param \Oxa\WistiaBundle\Entity\WistiaMediaThumbnail $thumbnail
     *
     * @return WistiaMedia
     */
    public function setThumbnail(\Oxa\WistiaBundle\Entity\WistiaMediaThumbnail $thumbnail = null)
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    /**
     * Get thumbnail
     *
     * @return \Oxa\WistiaBundle\Entity\WistiaMediaThumbnail
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }
}
