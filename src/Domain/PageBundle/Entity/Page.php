<?php

namespace Domain\PageBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Domain\PageBundle\Entity\Translation\PageTranslation;
use Domain\PageBundle\Model\PageInterface;
use Domain\ReportBundle\Util\DatesUtil;
use Domain\SiteBundle\Utils\Traits\SeoTrait;
use Oxa\Sonata\AdminBundle\Model\ChangeStateInterface;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\ChangeStateTrait;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Oxa\Sonata\MediaBundle\Entity\Media;
use Sonata\TranslationBundle\Model\Gedmo\TranslatableInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\OxaPersonalTranslatable as PersonalTranslatable;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Page
 *
 * @ORM\Table(name="page")
 * @ORM\Entity(repositoryClass="Domain\PageBundle\Repository\PageRepository")
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\TranslationEntity(class="Domain\PageBundle\Entity\Translation\PageTranslation")
 * @Assert\Callback(methods={"validatePageActionLink"})
 */
class Page implements DefaultEntityInterface, TranslatableInterface, PageInterface, ChangeStateInterface
{
    use DefaultEntityTrait;
    use PersonalTranslatable;
    use SeoTrait;
    use ChangeStateTrait;

    const POPULAR_CATEGORY_COUNT = 3;
    const POPULAR_CATEGORY_PAGE  = 1;
    const POPULAR_CATEGORY_STAT_PERIOD      = DatesUtil::RANGE_LAST_30_DAYS;
    const POPULAR_CATEGORY_AGGREGATE_PERIOD = DatesUtil::RANGE_TODAY;
    const POPULAR_CATEGORY_PREFIX = 'popular_category_';

    const CONTACT_SUBJECT_BUG = 'bug';
    const CONTACT_SUBJECT_ADS = 'ads';
    const CONTACT_SUBJECT_CREATE_BUSINESS = 'create_business';
    const CONTACT_SUBJECT_OTHER = 'other';

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
     * @ORM\Column(name="name", type="string", length=100, nullable=true)
     * @Assert\NotBlank()
     * @Assert\Length(max=100)
     */
    protected $name;

    /**
     * @var string - Page title (seo "H1" tag)
     *
     * @Gedmo\Translatable(fallback=true)
     * @ORM\Column(name="title", type="string", length=100)
     * @Assert\NotBlank()
     * @Assert\Length(max=100)
     */
    protected $title;

    /**
     * @var integer - Page code
     *
     * @ORM\Column(name="code", type="integer")
     */
    protected $code;

    /**
     * @var string - Page description (seo "copy" block)
     *
     * @Gedmo\Translatable(fallback=true)
     * @ORM\Column(name="description", type="string", length=100, nullable=true)
     * @Assert\Length(max=100)
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
     * @var bool - Using this checkbox a User may define whether to show a page.
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
     * @var string
     *
     * @ORM\Column(name="redirect_url", type="string", nullable=true, length=1000)
     * @Assert\Length(max=1000)
     * @Assert\Url()
     */
    protected $redirectUrl;

    /**
     * @var \DateTime
     * @ORM\Column(name="content_updated_at", type="datetime", nullable=true)
     */
    protected $contentUpdatedAt;

    /**
     * @var ArrayCollection - Page links
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\PageBundle\Entity\PageLink",
     *     mappedBy="page",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     * )
     * @Assert\Valid()
     */
    protected $links;

    /**
     * @var string - link
     *
     * @ORM\Column(name="action_link", type="string", length=1000, nullable=true)
     * @Assert\Url()
     * @Assert\Length(max=1000)
     */
    protected $actionLink;

    /**
     * @var boolean
     *
     * @ORM\Column(name="use_action_link", type="boolean", options={"default" : 0})
     */
    protected $useActionLink;

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
    public function __toString()
    {
        return $this->getName() ?: '';
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->translations = new ArrayCollection();
        $this->links        = new ArrayCollection();

        $this->useActionLink = false;
    }

    /**
     * @param string $name
     *
     * @return Page
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
     * Remove translation
     *
     * @param PageTranslation $translation
     */
    public function removeTranslation(PageTranslation $translation)
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

    /**
     * @param string $redirectUrl
     *
     * @return Page
     */
    public function setRedirectUrl($redirectUrl)
    {
        $this->redirectUrl = $redirectUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    /**
     * @return array
     */
    public static function getStaticPage()
    {
        return [
            PageInterface::CODE_CONTACT_US,
            PageInterface::CODE_PRIVACY_STATEMENT,
            PageInterface::CODE_TERMS_OF_USE,
            PageInterface::CODE_ADVERTISE,
        ];
    }

    /**
     * @param int $code
     *
     * @return array
     */
    public static function getPageSeoHintByCode($code)
    {
        switch ($code) {
            case PageInterface::CODE_ARTICLE_CATEGORY_LIST:
            case PageInterface::CODE_CATALOG_LOCALITY:
            case PageInterface::CODE_CATALOG_LOCALITY_CATEGORY:
            case PageInterface::CODE_EMERGENCY_AREA_CATEGORY:
                $validCode = $code;
                break;
            default:
                $validCode = PageInterface::CODE_DEFAULT;
                break;
        }

        return self::getPageSeoHint()[$validCode];
    }

    /**
     * @return array
     */
    public static function getPageSeoHint()
    {
        return [
            PageInterface::CODE_DEFAULT => [
                'title'             => 'page.help_message.default.title',
                'description'       => 'page.help_message.default.description',
                'seoTitle'          => 'page.help_message.default.seoTitle',
                'seoDescription'    => 'page.help_message.default.seoDescription',
                'actionLink'        => 'page.help_message.default.actionLink',
                'placeholders'      => [],
            ],
            PageInterface::CODE_ARTICLE_CATEGORY_LIST => [
                'title'             => 'page.help_message.default.title',
                'description'       => 'page.help_message.default.description',
                'seoTitle'          => 'page.help_message.article_category_list.seoTitle',
                'seoDescription'    => 'page.help_message.article_category_list.seoDescription',
                'placeholders'      => [
                    '[category]',
                ],
            ],
            PageInterface::CODE_CATALOG_LOCALITY => [
                'title'             => 'page.help_message.default.title',
                'description'       => 'page.help_message.default.description',
                'seoTitle'          => 'page.help_message.catalog_locality.seoTitle',
                'seoDescription'    => 'page.help_message.catalog_locality.seoDescription',
                'placeholders'      => self::getPopularCategoryPlaceholders(),
            ],
            PageInterface::CODE_CATALOG_LOCALITY_CATEGORY => [
                'title'             => 'page.help_message.default.title',
                'description'       => 'page.help_message.default.description',
                'seoTitle'          => 'page.help_message.catalog_locality_category.seoTitle',
                'seoDescription'    => 'page.help_message.catalog_locality_category.seoDescription',
                'placeholders'      => [
                    '[locality]',
                    '[category]',
                ],
            ],
            PageInterface::CODE_EMERGENCY_AREA_CATEGORY => [
                'title'             => 'page.help_message.default.title',
                'description'       => 'page.help_message.default.description',
                'seoTitle'          => 'page.help_message.emergency_area_category.seoTitle',
                'seoDescription'    => 'page.help_message.emergency_area_category.seoDescription',
                'placeholders'      => [
                    '[area]',
                    '[category]',
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public static function getPopularCategoryPlaceholders()
    {
        $placeholders = [];
        $placeholders[] = '[locality]';

        for ($i = 1; $i <= self::POPULAR_CATEGORY_COUNT; $i++) {
            $placeholders[] = self::getPopularCategoryKey($i);
        }

        return $placeholders;
    }

    /**
     * @param int $id
     *
     * @return string
     */
    public static function getPopularCategoryKey($id)
    {
        return sprintf('[%s%s]', self::POPULAR_CATEGORY_PREFIX, $id);
    }

    /**
     * @param int $code
     *
     * @return bool
     */
    public static function getShowContactForm($code)
    {
        return $code == self::CODE_CONTACT_US;
    }

    /**
     * @return array
     */
    public static function getContactSubjects()
    {
        return [
            self::CONTACT_SUBJECT_CREATE_BUSINESS => 'contact.form.subject_type.create_business',
            self::CONTACT_SUBJECT_BUG => 'contact.form.subject_type.bug',
            self::CONTACT_SUBJECT_ADS => 'contact.form.subject_type.ads',
            self::CONTACT_SUBJECT_OTHER => 'contact.form.subject_type.other',
        ];
    }

    /**
     * Sets contentUpdatedAt
     *
     * @param  \DateTime $updatedAt
     * @return $this
     */
    public function setContentUpdatedAt(\DateTime $updatedAt)
    {
        $this->contentUpdatedAt = $updatedAt;

        return $this;
    }

    /**
     * Returns contentUpdatedAt
     *
     * @return \DateTime
     */
    public function getContentUpdatedAt()
    {
        return $this->contentUpdatedAt;
    }

    /**
     * Add $link
     *
     * @param PageLink $link
     *
     * @return Page
     */
    public function addLink(PageLink $link)
    {
        $this->links->add($link);
        $link->setPage($this);

        return $this;
    }

    /**
     * Remove $link
     *
     * @param PageLink $link
     */
    public function removeLink(PageLink $link)
    {
        $this->links->removeElement($link);
    }

    /**
     * @return ArrayCollection
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * @param string $link
     *
     * @return Page
     */
    public function setActionLink($link)
    {
        $this->actionLink = $link;

        return $this;
    }

    /**
     * @return string
     */
    public function getActionLink()
    {
        return $this->actionLink;
    }

    /**
     * @param boolean $useActionLink
     *
     * @return Page
     */
    public function setUseActionLink($useActionLink)
    {
        $this->useActionLink = $useActionLink;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getUseActionLink()
    {
        return $this->useActionLink;
    }

    /**
     * @param ExecutionContextInterface $context
     */
    public function validatePageActionLink(ExecutionContextInterface $context)
    {
        if ($this->getUseActionLink() and !trim($this->getActionLink())) {
            $context->buildViolation('page.action_url.required')
                ->atPath('actionLink')
                ->addViolation()
            ;
        }
    }

    /**
     * @return PageLink[]
     */
    public function getLinksGroupedByTypes()
    {
        $links = [];

        foreach ($this->getLinks() as $link) {
            $links[$link->getType()][] = $link;
        }

        return $links;
    }
}
