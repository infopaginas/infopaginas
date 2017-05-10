<?php

namespace Domain\PageBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Domain\PageBundle\Model\PageInterface;
use Domain\SiteBundle\Utils\Traits\SeoTrait;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Oxa\Sonata\MediaBundle\Entity\Media;
use Sonata\TranslationBundle\Model\Gedmo\TranslatableInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\OxaPersonalTranslatable as PersonalTranslatable;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Page
 *
 * @ORM\Table(name="page")
 * @ORM\Entity(repositoryClass="Domain\PageBundle\Repository\PageRepository")
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\TranslationEntity(class="Domain\PageBundle\Entity\Translation\PageTranslation")
 */
class Page implements DefaultEntityInterface, TranslatableInterface, PageInterface
{
    use DefaultEntityTrait;
    use PersonalTranslatable;
    use SeoTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string - Page title
     *
     * @Gedmo\Translatable(fallback=true)
     * @ORM\Column(name="title", type="string", length=100)
     * @Assert\NotBlank()
     */
    protected $title;

    /**
     * @var integer - Page code
     *
     * @ORM\Column(name="code", type="integer")
     */
    protected $code;

    /**
     * @var string - Page description
     *
     * @Gedmo\Translatable(fallback=true)
     * @ORM\Column(name="description", type="string", length=100, nullable=true)
     */
    protected $description;

    /**
     * @var string - Body
     *
     * @Gedmo\Translatable(fallback=true)
     * @ORM\Column(name="body", type="text")
     */
    protected $body;

    /**
     * @var string - Using this checkbox a User may define whether to show a page.
     *
     * @ORM\Column(name="is_published", type="boolean", options={"default" : 0})
     */
    protected $isPublished = false;

    /**
     * @var string - Used to create human like url
     *
     * @Gedmo\Slug(fields={"title"}, updatable=false)
     * @ORM\Column(name="slug", type="string")
     */
    protected $slug;

    /**
     * @var Template
     *
     * @ORM\ManyToOne(targetEntity="Domain\PageBundle\Entity\Template",
     *     inversedBy="pages",
     *     cascade={"persist"}
     *     )
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id")
     */
    protected $template;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\PageBundle\Entity\Translation\PageTranslation",
     *     mappedBy="object",
     *     cascade={"persist", "remove"}
     * )
     */
    protected $translations;

    /**
     * @var Media - Media Background Image
     * @ORM\ManyToOne(targetEntity="Oxa\Sonata\MediaBundle\Entity\Media",
     *     inversedBy="backgroundPages",
     *     cascade={"persist"}
     *     )
     * @ORM\JoinColumn(name="background_id", referencedColumnName="id", nullable=true)
     */
    protected $background;

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
        return $this->getTitle() ?: '';
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->translations = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Page
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
     * @return Page
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
     * Set body
     *
     * @param string $body
     *
     * @return Page
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
     * Set isPublished
     *
     * @param boolean $isPublished
     *
     * @return Page
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

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Page
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set template
     *
     * @param \Domain\PageBundle\Entity\Template $template
     *
     * @return Page
     */
    public function setTemplate(\Domain\PageBundle\Entity\Template $template = null)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get template
     *
     * @return \Domain\PageBundle\Entity\Template
     */
    public function getTemplate()
    {
        return $this->template;
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

    /**
     * Set code
     *
     * @param integer $code
     *
     * @return Page
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return integer
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set background
     *
     * @param Media $background
     *
     * @return Page
     */
    public function setBackground($background = null)
    {
        $this->background = $background;

        return $this;
    }

    /**
     * Get background
     *
     * @return Media
     */
    public function getBackground()
    {
        return $this->background;
    }
}
