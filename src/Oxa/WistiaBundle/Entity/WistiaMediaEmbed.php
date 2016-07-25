<?php
/**
 * Created by PhpStorm.
 * User: Xedin
 * Date: 18.06.16
 * Time: 12:20
 */

namespace Oxa\WistiaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WistiaMedia
 *
 * @ORM\Table(name="wistia_media_embeds")
 * @ORM\Entity(repositoryClass="Oxa\WistiaBundle\Repository\WistiaMediaEmbedRepository")
 */
class WistiaMediaEmbed
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
     * @var string
     *
     * @ORM\Column(name="version", type="string", length=100)
     */
    private $version;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=100)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="html", type="text")
     */
    private $html;

    /**
     * @var integer
     *
     * @ORM\Column(name="width", type="integer")
     */
    private $width;

    /**
     * @var integer
     *
     * @ORM\Column(name="height", type="integer")
     */
    private $height;

    /**
     * @var string
     *
     * @ORM\Column(name="provider_name", type="string", length=100)
     */
    private $providerName;

    /**
     * @var string
     *
     * @ORM\Column(name="provider_url", type="string", length=100)
     */
    private $providerURL;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="thumbnail_url", type="string", length=255)
     */
    private $thumbnailURL;

    /**
     * @var integer
     *
     * @ORM\Column(name="thumbnail_width", type="integer")
     */
    private $thumbnailWidth;

    /**
     * @var integer
     *
     * @ORM\Column(name="thumbnail_height", type="integer")
     */
    private $thumbnailHeight;

    /**
     * @var float
     *
     * @ORM\Column(name="duration", type="float")
     */
    private $duration;

    /**
     * @var bool
     *
     * @ORM\Column(name="auto_dimensions", type="boolean")
     */
    private $autoDimensions;

    /**
     * @var WistiaMedia
     *
     * @ORM\ManyToOne(targetEntity="WistiaMedia")
     * @ORM\JoinColumn(name="wistia_media_id", referencedColumnName="id")
     */
    private $media;

    public function __construct(array $embedAPIData, WistiaMedia $media)
    {
        $this->setVersion($embedAPIData['version']);
        $this->setType($embedAPIData['type']);
        $this->setHtml($embedAPIData['html']);
        $this->setWidth($embedAPIData['width']);
        $this->setHeight($embedAPIData['height']);
        $this->setProviderName($embedAPIData['provider_name']);
        $this->setProviderURL($embedAPIData['provider_url']);
        $this->setTitle($embedAPIData['title']);
        $this->setThumbnailURL($embedAPIData['thumbnail_url']);
        $this->setThumbnailWidth($embedAPIData['thumbnail_width']);
        $this->setThumbnailHeight($embedAPIData['thumbnail_height']);
        $this->setDuration($embedAPIData['duration']);
        $this->setMedia($media);

        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set version
     *
     * @param string $version
     *
     * @return WistiaMediaEmbed
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return WistiaMediaEmbed
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
     * Set html
     *
     * @param string $html
     *
     * @return WistiaMediaEmbed
     */
    public function setHtml($html)
    {
        $this->html = $html;

        return $this;
    }

    /**
     * Get html
     *
     * @return string
     */
    public function getHtml()
    {
        return $this->html;
    }

    /**
     * Set width
     *
     * @param integer $width
     *
     * @return WistiaMediaEmbed
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Get width
     *
     * @return integer
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set height
     *
     * @param integer $height
     *
     * @return WistiaMediaEmbed
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Get height
     *
     * @return integer
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set providerName
     *
     * @param string $providerName
     *
     * @return WistiaMediaEmbed
     */
    public function setProviderName($providerName)
    {
        $this->providerName = $providerName;

        return $this;
    }

    /**
     * Get providerName
     *
     * @return string
     */
    public function getProviderName()
    {
        return $this->providerName;
    }

    /**
     * Set providerURL
     *
     * @param string $providerURL
     *
     * @return WistiaMediaEmbed
     */
    public function setProviderURL($providerURL)
    {
        $this->providerURL = $providerURL;

        return $this;
    }

    /**
     * Get providerURL
     *
     * @return string
     */
    public function getProviderURL()
    {
        return $this->providerURL;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return WistiaMediaEmbed
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set thumbnailURL
     *
     * @param string $thumbnailURL
     *
     * @return WistiaMediaEmbed
     */
    public function setThumbnailURL($thumbnailURL)
    {
        $this->thumbnailURL = $thumbnailURL;

        return $this;
    }

    /**
     * Get thumbnailURL
     *
     * @return string
     */
    public function getThumbnailURL()
    {
        return $this->thumbnailURL;
    }

    /**
     * Set thumbnailWidth
     *
     * @param integer $thumbnailWidth
     *
     * @return WistiaMediaEmbed
     */
    public function setThumbnailWidth($thumbnailWidth)
    {
        $this->thumbnailWidth = $thumbnailWidth;

        return $this;
    }

    /**
     * Get thumbnailWidth
     *
     * @return integer
     */
    public function getThumbnailWidth()
    {
        return $this->thumbnailWidth;
    }

    /**
     * Set thumbnailHeight
     *
     * @param integer $thumbnailHeight
     *
     * @return WistiaMediaEmbed
     */
    public function setThumbnailHeight($thumbnailHeight)
    {
        $this->thumbnailHeight = $thumbnailHeight;

        return $this;
    }

    /**
     * Get thumbnailHeight
     *
     * @return integer
     */
    public function getThumbnailHeight()
    {
        return $this->thumbnailHeight;
    }

    /**
     * Set duration
     *
     * @param float $duration
     *
     * @return WistiaMediaEmbed
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return float
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set media
     *
     * @param \Oxa\WistiaBundle\Entity\WistiaMedia $media
     *
     * @return WistiaMediaEmbed
     */
    public function setMedia(\Oxa\WistiaBundle\Entity\WistiaMedia $media = null)
    {
        $this->media = $media;

        return $this;
    }

    /**
     * Get media
     *
     * @return \Oxa\WistiaBundle\Entity\WistiaMedia
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * Set autoDimensions
     *
     * @param boolean $autoDimensions
     *
     * @return WistiaMediaEmbed
     */
    public function setAutoDimensions($autoDimensions)
    {
        $this->autoDimensions = $autoDimensions;

        return $this;
    }

    /**
     * Get autoDimensions
     *
     * @return boolean
     */
    public function getAutoDimensions()
    {
        return $this->autoDimensions;
    }
}
