<?php

namespace Domain\ArticleBundle\Entity\Media;

use Oxa\Sonata\AdminBundle\Model\ChangeStateInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\ChangeStateTrait;
use Sonata\TranslationBundle\Traits\Gedmo\PersonalTranslatableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Domain\ArticleBundle\Entity\Article;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Sonata\TranslationBundle\Model\Gedmo\TranslatableInterface;

/**
 * ArticleGallery
 *
 * @ORM\Table(name="article_gallery")
 * @ORM\Entity(repositoryClass="Domain\ArticleBundle\Repository\ArticleGalleryRepository")
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\TranslationEntity(class="Domain\ArticleBundle\Entity\Translation\Media\ArticleGalleryTranslation")
 */
class ArticleGallery implements DefaultEntityInterface, TranslatableInterface, ChangeStateInterface
{
    use DefaultEntityTrait;
    use PersonalTranslatableTrait;
    use ChangeStateTrait;

    const TRANSLATION_FIELD_DESCRIPTION = 'description';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string - Description of Image
     *
     * @Gedmo\Translatable(fallback=true)
     * @ORM\Column(name="description", type="text", length=1000, nullable=true)
     * @Assert\NotBlank()
     */
    protected $description;

    /**
     * @var Article
     * @ORM\ManyToOne(targetEntity="Domain\ArticleBundle\Entity\Article",
     *     cascade={"persist"},
     *     inversedBy="images"
     * )
     * @ORM\JoinColumn(name="article_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $article;

    /**
     * @var \Oxa\Sonata\MediaBundle\Entity\Media
     * @ORM\ManyToOne(targetEntity="Oxa\Sonata\MediaBundle\Entity\Media",
     *     inversedBy="articleGallery",
     *     cascade={"persist"}
     * )
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id", nullable=false)
     * @Assert\Valid()
     * @Assert\NotBlank()
     */
    protected $media;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\Translation\Media\BusinessGalleryTranslation",
     *     mappedBy="object",
     *     cascade={"persist", "remove"}
     * )
     */
    protected $translations;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * @param Article $article
     */
    public function setArticle(Article $article)
    {
        $this->article = $article;
    }

    /**
     * @return \Oxa\Sonata\MediaBundle\Entity\Media
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * @param \Oxa\Sonata\MediaBundle\Entity\Media $media
     */
    public function setMedia(\Oxa\Sonata\MediaBundle\Entity\Media $media = null)
    {
        $this->media = $media;
    }

    /**
     * @return ArrayCollection
     */
    public function getTranslations(): ArrayCollection
    {
        return $this->translations;
    }

    /**
     * @param ArrayCollection $translations
     */
    public function setTranslations(ArrayCollection $translations)
    {
        $this->translations = $translations;
    }
}
