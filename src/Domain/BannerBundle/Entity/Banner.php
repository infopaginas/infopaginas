<?php

namespace Domain\BannerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Domain\BannerBundle\Model\TypeModel;
use Oxa\Sonata\AdminBundle\Model\ChangeStateInterface;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\ChangeStateTrait;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Banner
 *
 * @ORM\Table(name="banner")
 * @ORM\Entity(repositoryClass="Domain\BannerBundle\Repository\BannerRepository")
 */
class Banner implements DefaultEntityInterface, ChangeStateInterface
{
    use DefaultEntityTrait;
    use ChangeStateTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string - Banner title
     *
     * @ORM\Column(name="title", type="string", length=100)
     * @Assert\NotBlank()
     */
    protected $title;

    /**
     * @var string - Banner description
     *
     * @ORM\Column(name="description", type="text", length=100)
     * @Assert\NotBlank()
     */
    protected $description;

    /**
     * @var string - Placement
     *
     * @ORM\Column(name="placement", type="string", length=100, options={"default" : ""})
     */
    protected $placement;

    /**
     * @var string - Comment
     *
     * @ORM\Column(name="comment", type="text", length=1000, options={"default" : ""})
     */
    protected $comment;

    /**
     * @var integer
     *
     * @ORM\Column(name="code", type="integer", options={"default" : 0})
     */
    protected $code;

    /**
     * @var string
     *
     * @ORM\Column(name="html_id", type="string", length=100, options={"default" : 0})
     */
    protected $htmlId;

    /**
     * @var string
     *
     * @ORM\Column(name="slot_id", type="string", length=100, options={"default" : 0})
     */
    protected $slotId;

    /**
     * @var string - Using this checkbox a Admin may define whether to show a banner block.
     *
     * @ORM\Column(name="is_published", type="boolean", options={"default" : 0})
     */
    protected $isPublished;

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
     * Constructor
     */
    public function __construct()
    {
        $this->isPublished  = false;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle() ?: '';
    }

    /**
     * @return string
     */
    public function getSize()
    {
        return TypeModel::getSizeByCode($this->getCode());
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Banner
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
     * Set description
     *
     * @param string $description
     *
     * @return Banner
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
     * @param string $placement
     *
     * @return Banner
     */
    public function setPlacement($placement)
    {
        $this->placement = $placement;

        return $this;
    }

    /**
     * @return string
     */
    public function getPlacement()
    {
        return $this->placement;
    }

    /**
     * @param string $comment
     *
     * @return Banner
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param int $code
     *
     * @return Banner
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $htmlId
     *
     * @return Banner
     */
    public function setHtmlId($htmlId)
    {
        $this->htmlId = $htmlId;

        return $this;
    }

    /**
     * @return string
     */
    public function getHtmlId()
    {
        return $this->htmlId;
    }

    /**
     * @param string $slotId
     *
     * @return Banner
     */
    public function setSlotId($slotId)
    {
        $this->slotId = $slotId;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlotId()
    {
        return $this->slotId;
    }

    /**
     * Set isPublished
     *
     * @param boolean $isPublished
     *
     * @return Banner
     */
    public function setIsPublished($isPublished)
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    /**
     * Get isPublished
     *
     * @return boolean
     */
    public function getIsPublished()
    {
        return $this->isPublished;
    }
}
