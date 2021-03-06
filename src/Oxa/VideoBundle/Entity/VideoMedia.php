<?php

namespace Oxa\VideoBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\HomepageCarousel;
use Oxa\Sonata\AdminBundle\Model\ChangeStateInterface;
use Oxa\Sonata\AdminBundle\Model\PostponeRemoveInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\ChangeStateTrait;
use Oxa\Sonata\AdminBundle\Util\Traits\PostponeRemoveTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Oxa\Sonata\MediaBundle\Entity\Media;

/**
 * VideoMedia
 *
 * @ORM\Table(name="video_media")
 * @ORM\Entity(repositoryClass="Oxa\VideoBundle\Repository\VideoMediaRepository")
 */
class VideoMedia implements PostponeRemoveInterface, ChangeStateInterface
{
    use PostponeRemoveTrait;
    use ChangeStateTrait;

    public const YOUTUBE_ACTION_ADD     = 'YOUTUBE_ACTION_ADD';
    public const YOUTUBE_ACTION_UPDATE  = 'YOUTUBE_ACTION_UPDATE';
    public const YOUTUBE_ACTION_REMOVE  = 'YOUTUBE_ACTION_REMOVE';
    public const YOUTUBE_ACTION_ERROR   = 'YOUTUBE_ACTION_ERROR';

    public const VIDEO_TITLE_MAX_LENGTH      = 255;
    public const VIDEO_TITLE_MAX_DESCRIPTION = 255;

    public const VIDEO_STATUS_PENDING = 'pending';
    public const VIDEO_STATUS_ACTIVE  = 'active';
    public const VIDEO_STATUS_ERROR   = 'error';

    public const INVALID_YOUTUBE_ID_STATUS = 'invalid_id';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * Related to VIDEO_TITLE_MAX_LENGTH
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     * @Assert\Length(max=255, maxMessage="business_profile.max_length")
     */
    private $title;

    /**
     * Related to VIDEO_TITLE_MAX_DESCRIPTION
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     * @Assert\Length(max=255, maxMessage="business_profile.max_length")
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="filename", type="string", length=255, nullable=true)
     */
    private $filename;

    /**
     * @var string
     *
     * @ORM\Column(name="filepath", type="string", length=255, nullable=true)
     */
    private $filepath;

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
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="youtube_id", type="string", length=255, nullable=true)
     */
    private $youtubeId;

    /**
     * @var bool
     *
     * @ORM\Column(name="youtube_support", type="boolean", nullable=true)
     */
    private $youtubeSupport;

    /**
     * @var string
     *
     * @ORM\Column(name="youtube_action", type="string", length=255, nullable=true)
     */
    private $youtubeAction;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="Domain\BusinessBundle\Entity\BusinessProfile",
     *      mappedBy="video"
     * )
     */
    protected $businessProfiles;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="Domain\BusinessBundle\Entity\HomepageCarousel",
     *      mappedBy="video"
     * )
     */
    protected $homepageCarousel;

    /**
     * Logo Field. Related to class constant BUSINESS_PROFILE_FIELD_LOGO
     *
     * @var Media - Media poster
     * @ORM\ManyToOne(targetEntity="Oxa\Sonata\MediaBundle\Entity\Media",
     *     inversedBy="videoMedia",
     *     cascade={"persist"}
     *     )
     * @ORM\JoinColumn(name="poster_id", referencedColumnName="id", nullable=true)
     */
    protected $poster;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    protected $uploadStatus;

    public function __construct(array $videoMediaData = [])
    {
        if (!empty($videoMediaData)) {
            $this->setName($videoMediaData['name']);
            $this->setType($videoMediaData['type']);
            $this->setFilename($videoMediaData['filename']);
            $this->setFilepath($videoMediaData['filepath']);
        }

        $this->setStatus($this::VIDEO_STATUS_PENDING);

        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt(new \DateTime());

        $this->setYoutubeSupport(true);
        $this->setYoutubeAction(null);

        $this->businessProfiles = new ArrayCollection();
        $this->homepageCarousel = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if ($this->getTitle()) {
            $name = $this->getTitle();
        } elseif ($this->getName()) {
            $name = $this->getName();
        } else {
            $name = $this->getId();
        }

        return (string) $name;
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
     * Set id
     *
     * @return int
     */
    public function setId($id)
    {
        return $this->id = $id;
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
     * @param string $title
     *
     * @return VideoMedia
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
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
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
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
     * Set filename
     *
     * @param string $filename
     *
     * @return VideoMedia
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * Get filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set filename
     *
     * @param string $filename
     *
     * @return VideoMedia
     */
    public function setFilepath($filepath)
    {
        $this->filepath = $filepath;
        return $this;
    }

    /**
     * Get filename
     *
     * @return string
     */
    public function getFilepath()
    {
        return $this->filepath;
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
     * @return ArrayCollection
     */
    public function getBusinessProfiles()
    {
        return $this->businessProfiles;
    }

    /**
     * @param mixed $businessProfiles
     *
     * @return VideoMedia
     */
    public function setBusinessProfiles($businessProfiles)
    {
        $this->businessProfiles = $businessProfiles;

        return $this;
    }

    /**
     * Add businessProfile
     *
     * @param BusinessProfile $businessProfile
     *
     * @return VideoMedia
     */
    public function addBusinessProfiles(BusinessProfile $businessProfile)
    {
        $this->businessProfiles[] = $businessProfile;
    }

    /**
     * @param BusinessProfile $businessProfile
     *
     * @return $this
     */
    public function removeBusinessProfiles(BusinessProfile $businessProfile)
    {
        $this->businessProfiles->removeElement($businessProfile);

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getHomepageCarousel()
    {
        return $this->homepageCarousel;
    }

    /**
     * @param mixed $homepageCarousel
     *
     * @return VideoMedia
     */
    public function setHomepageCarousel($homepageCarousel)
    {
        $this->homepageCarousel = $homepageCarousel;

        return $this;
    }

    /**
     * Add homepageCarousel
     *
     * @param HomepageCarousel $homepageCarousel
     *
     * @return VideoMedia
     */
    public function addHomepageCarousel(HomepageCarousel $homepageCarousel)
    {
        $this->homepageCarousel[] = $homepageCarousel;
    }

    /**
     * @param HomepageCarousel $homepageCarousel
     *
     * @return $this
     */
    public function removeHomepageCarousel(HomepageCarousel $homepageCarousel)
    {
        $this->homepageCarousel->removeElement($homepageCarousel);

        return $this;
    }

    /**
     * @param string $youtubeId
     *
     * @return VideoMedia
     */
    public function setYoutubeId($youtubeId)
    {
        $this->youtubeId = $youtubeId;

        return $this;
    }

    /**
     * @return string
     */
    public function getYoutubeId()
    {
        return $this->youtubeId;
    }

    /**
     * @param bool $youtubeSupport
     *
     * @return VideoMedia
     */
    public function setYoutubeSupport($youtubeSupport)
    {
        $this->youtubeSupport = $youtubeSupport;

        return $this;
    }

    /**
     * @return bool
     */
    public function getYoutubeSupport()
    {
        return $this->youtubeSupport;
    }

    /**
     * @param string $youtubeAction
     *
     * @return VideoMedia
     */
    public function setYoutubeAction($youtubeAction)
    {
        $this->youtubeAction = $youtubeAction;

        return $this;
    }

    /**
     * @return string
     */
    public function getYoutubeAction()
    {
        return $this->youtubeAction;
    }

    /**
     * Set poster
     *
     * @param Media|null $poster
     *
     * @return VideoMedia
     */
    public function setPoster($poster = null)
    {
        $this->poster = $poster;

        return $this;
    }

    /**
     * Get poster
     *
     * @return Media
     */
    public function getPoster()
    {
        return $this->poster;
    }

    /**
     * @return string
     */
    public function getYoutubeTitle()
    {
        $title = '';

        if ($this->getTitle()) {
            $title = $this->getTitle();
        } else {
            if (!$this->getBusinessProfiles()->isEmpty()) {
                /* @var BusinessProfile $business */
                $business = $this->getBusinessProfiles()->first();

                $title = $business->getSeoTitle();
            }
        }

        return $title;
    }

    /**
     * @return string
     */
    public function getYoutubeDescription()
    {
        $title = '';

        if ($this->getDescription()) {
            $title = $this->getDescription();
        } else {
            if (!$this->getBusinessProfiles()->isEmpty()) {
                /* @var BusinessProfile $business */
                $business = $this->getBusinessProfiles()->first();

                $title = $business->getSeoDescription();
            }
        }

        return $title;
    }

    public function getUploadStatus()
    {
        return $this->uploadStatus;
    }

    public function setUploadStatus($uploadStatus)
    {
        $this->uploadStatus = $uploadStatus;
        return $this;
    }
}
