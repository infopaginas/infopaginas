<?php

namespace Oxa\VideoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VideoMedia
 *
 * @ORM\Table(name="wistia_medias")
 * @ORM\Entity(repositoryClass="Oxa\VideoBundle\Repository\VideoMediaRepository")
 */
class VideoMedia
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="wistia_id", type="integer")
     */
    private $videoId;

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
     * @ORM\Column(name="description", type="text", nullable=true)
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
     * @ORM\OneToOne(targetEntity="VideoMediaThumbnail", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="thumbnail_id", referencedColumnName="id")
     */
    private $thumbnail;

    /**
     * @ORM\OneToMany(targetEntity="VideoMediaEmbed", mappedBy="media")
     */
    private $mediaEmbeds;

    public function __construct(array $videoMediaData = [])
    {
        if (!empty($videoMediaData)) {
            $this->setVideoId($videoMediaData['id']);
            $this->setName($videoMediaData['name']);
            $this->setType($videoMediaData['type']);
            $this->setCreatedAt(new \DateTime($videoMediaData['created']));
            $this->setUpdatedAt(new \DateTime($videoMediaData['updated']));
            $this->setHashedId($videoMediaData['hashed_id']);
            $this->setDescription($videoMediaData['description']);
            $this->setProgress($videoMediaData['progress']);
            $this->setStatus($videoMediaData['status']);
            $this->setAccountId($videoMediaData['account_id']);

            $thumbnail = new VideoMediaThumbnail($videoMediaData['thumbnail']);
            $this->setThumbnail($thumbnail);
        }

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
     * Set VideoId
     *
     * @param integer $VideoId
     *
     * @return VideoMedia
     */
    public function setVideoId($videoId)
    {
        $this->videoId = $videoId;

        return $this;
    }

    /**
     * Get VideoId
     *
     * @return integer
     */
    public function getVideoId()
    {
        return $this->videoId;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return VideoMedia
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
     * @return VideoMedia
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
     * @return VideoMedia
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
     * @return VideoMedia
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
     * @return VideoMedia
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
     * @return VideoMedia
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
     * @return VideoMedia
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
     * @return VideoMedia
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
     * @return VideoMedia
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
     * @param \Oxa\VideoBundle\Entity\VideoMediaThumbnail $thumbnail
     *
     * @return VideoMedia
     */
    public function setThumbnail(\Oxa\VideoBundle\Entity\VideoMediaThumbnail $thumbnail = null)
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    /**
     * Get thumbnail
     *
     * @return \Oxa\VideoBundle\Entity\VideoMediaThumbnail
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * Add mediaEmbed
     *
     * @param \Oxa\VideoBundle\Entity\VideoMediaEmbed $mediaEmbed
     *
     * @return VideoMedia
     */
    public function addMediaEmbed(\Oxa\VideoBundle\Entity\VideoMediaEmbed $mediaEmbed)
    {
        $this->mediaEmbeds[] = $mediaEmbed;

        return $this;
    }

    /**
     * Remove mediaEmbed
     *
     * @param \Oxa\VideoBundle\Entity\VideoMediaEmbed $mediaEmbed
     */
    public function removeMediaEmbed(\Oxa\VideoBundle\Entity\VideoMediaEmbed $mediaEmbed)
    {
        $this->mediaEmbeds->removeElement($mediaEmbed);
    }

    /**
     * Get mediaEmbeds
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMediaEmbeds()
    {
        return $this->mediaEmbeds;
    }
}
