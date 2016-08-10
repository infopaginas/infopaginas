<?php

namespace Oxa\WistiaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WistiaMediaThumbnail
 *
 * @ORM\Table(name="wistia_media_thumbnails")
 * @ORM\Entity(repositoryClass="Oxa\WistiaBundle\Repository\WistiaMediaThumbnailRepository")
 */
class WistiaMediaThumbnail
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
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

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

    public function __construct(array $wistiaMediaThumbnailData)
    {
        $this->setUrl($wistiaMediaThumbnailData['url']);
        $this->setWidth($wistiaMediaThumbnailData['width']);
        $this->setHeight($wistiaMediaThumbnailData['height']);

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
     * Set url
     *
     * @param string $url
     *
     * @return WistiaMediaThumbnail
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set width
     *
     * @param integer $width
     *
     * @return WistiaMediaThumbnail
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
     * @return WistiaMediaThumbnail
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
}
