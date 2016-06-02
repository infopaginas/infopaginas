<?php

namespace Domain\BannerBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Domain\BannerBundle\Model\TypeModel;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Gedmo\Mapping\Annotation as Gedmo;
use Sonata\TranslationBundle\Model\Gedmo\TranslatableInterface;
use Sonata\TranslationBundle\Traits\Gedmo\PersonalTranslatable;

/**
 * Type
 *
 * @ORM\Table(name="type")
 * @ORM\Entity(repositoryClass="Domain\BannerBundle\Repository\TypeRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @Gedmo\TranslationEntity(class="Domain\BannerBundle\Entity\Translation\TypeTranslation")
 */
class Type extends TypeModel implements DefaultEntityInterface, TranslatableInterface
{
    use DefaultEntityTrait;
    use PersonalTranslatable;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string - Banner type name
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="name", type="string", length=100)
     */
    protected $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="code", type="integer", nullable=true)
     */
    protected $code;

    /**
     * @var string - Placement
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="placement", type="string", length=100)
     */
    protected $placement;

    /**
     * @var string - Comment
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="comment", type="text", length=1000)
     */
    protected $comment;

    /**
     * @var Banner[]
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\BannerBundle\Entity\Banner",
     *     mappedBy="type",
     *     cascade={"persist", "remove"}
     *     )
     */
    protected $banners;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\BannerBundle\Entity\Translation\BannerTranslation",
     *     mappedBy="object",
     *     cascade={"persist", "remove"}
     * )
     */
    protected $translations;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function __toString()
    {
        switch (true) {
            case $this->getName():
                $result = $this->getName();
                break;
            case $this->getId():
                $result = sprintf('id(%s): not translated', $this->getId());
                break;
            default:
                $result = 'New type';
        }
        return $result;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->banners = new \Doctrine\Common\Collections\ArrayCollection();
        $this->translations = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Type
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
     * Set placement
     *
     * @param string $placement
     *
     * @return Type
     */
    public function setPlacement($placement)
    {
        $this->placement = $placement;

        return $this;
    }

    /**
     * Get placement
     *
     * @return string
     */
    public function getPlacement()
    {
        return $this->placement;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return Type
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Add banner
     *
     * @param \Domain\BannerBundle\Entity\Banner $banner
     *
     * @return Type
     */
    public function addBanner(\Domain\BannerBundle\Entity\Banner $banner)
    {
        $this->banners[] = $banner;

        return $this;
    }

    /**
     * Remove banner
     *
     * @param \Domain\BannerBundle\Entity\Banner $banner
     */
    public function removeBanner(\Domain\BannerBundle\Entity\Banner $banner)
    {
        $this->banners->removeElement($banner);
    }

    /**
     * Get banners
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBanners()
    {
        return $this->banners;
    }

    /**
     * Remove translation
     *
     * @param \Domain\BannerBundle\Entity\Translation\BannerTranslation $translation
     */
    public function removeTranslation(\Domain\BannerBundle\Entity\Translation\BannerTranslation $translation)
    {
        $this->translations->removeElement($translation);
    }
}
