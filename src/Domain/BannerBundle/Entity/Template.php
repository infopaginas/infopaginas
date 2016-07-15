<?php

namespace Domain\BannerBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Gedmo\Mapping\Annotation as Gedmo;
use Sonata\TranslationBundle\Model\Gedmo\TranslatableInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\OxaPersonalTranslatable as PersonalTranslatable;

/**
 * Template
 *
 * @ORM\Table(name="banner_template")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Entity(repositoryClass="Domain\BannerBundle\Repository\TemplateRepository")
 * @Gedmo\TranslationEntity(class="Domain\BannerBundle\Entity\Translation\TemplateTranslation")
 */
class Template implements DefaultEntityInterface, TranslatableInterface
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
     * @var string - Script template name
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="name", type="string", length=100)
     */
    protected $name;

    /**
     * @var string - Script template body
     *
     * @ORM\Column(name="body", type="text")
     */
    protected $body;

    /**
     * @var Banner[]
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\BannerBundle\Entity\Banner",
     *     mappedBy="template",
     *     cascade={"persist", "remove"}
     *     )
     */
    protected $banners;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\BannerBundle\Entity\Translation\TemplateTranslation",
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
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->banners = new \Doctrine\Common\Collections\ArrayCollection();
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
                $result = 'New template';
        }
        return $result;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Template
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
     * Set body
     *
     * @param string $body
     *
     * @return Template
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Add banner
     *
     * @param \Domain\BannerBundle\Entity\Banner $banner
     *
     * @return Template
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
     * Add translation
     *
     * @param \Domain\BannerBundle\Entity\Translation\TemplateTranslation $translation
     *
     * @return Template
     */
    public function addTranslation(\Domain\BannerBundle\Entity\Translation\TemplateTranslation $translation)
    {
        $this->translations[] = $translation;

        return $this;
    }

    /**
     * Remove translation
     *
     * @param \Domain\BannerBundle\Entity\Translation\TemplateTranslation $translation
     */
    public function removeTranslation(\Domain\BannerBundle\Entity\Translation\TemplateTranslation $translation)
    {
        $this->translations->removeElement($translation);
    }

    /**
     * Get translations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTranslations()
    {
        return $this->translations;
    }
}
