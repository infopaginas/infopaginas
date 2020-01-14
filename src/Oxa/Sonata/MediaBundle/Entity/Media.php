<?php

namespace Oxa\Sonata\MediaBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Domain\ArticleBundle\Entity\Media\ArticleGallery;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Coupon;
use Domain\ArticleBundle\Entity\Article;
use Domain\BusinessBundle\Entity\HomepageCarousel;
use Domain\BusinessBundle\Entity\Media\BusinessGallery;
use Domain\BusinessBundle\Entity\PaymentMethod;
use Domain\BusinessBundle\Entity\Testimonial;
use Oxa\Sonata\AdminBundle\Model\ChangeStateInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\ChangeStateTrait;
use Oxa\VideoBundle\Entity\VideoMedia;
use Domain\PageBundle\Entity\Page;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Model\PostponeRemoveInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Oxa\Sonata\AdminBundle\Util\Traits\PostponeRemoveTrait;
use Oxa\Sonata\MediaBundle\Model\OxaMediaInterface;
use Sonata\MediaBundle\Entity\BaseMedia as BaseMedia;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Table(name="media__media")
 * @ORM\Entity(repositoryClass="Oxa\Sonata\MediaBundle\Repository\MediaRepository")
 * @ORM\HasLifecycleCallbacks
 * @Assert\Callback(methods={"validateMediaSize"})
 */
class Media extends BaseMedia implements
    OxaMediaInterface,
    DefaultEntityInterface,
    PostponeRemoveInterface,
    ChangeStateInterface
{
    use DefaultEntityTrait;
    use PostponeRemoveTrait;
    use ChangeStateTrait;

    const UPLOADS_DIR_NAME = 'uploads';

    /**
     * Image max size in bytes = 10Mb
     */
    const IMAGE_MAX_SIZE = 10485760;

    /**
     * Background max size in bytes = 1Mb
     */
    const IMAGE_BACKGROUND_MAX_SIZE = 1048576;

    /**
     * Payment method image max size in bytes = 10kb
     */
    const IMAGE_PAYMENT_METHOD_MAX_SIZE = 10240;

    const BYTES_IN_MEGABYTE = 1048576;
    const BYTES_IN_KILOBYTE = 1024;

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
     * @ORM\Column(name="url", type="string", nullable=true)
     * @Assert\Valid()
     */
    protected $url;

    /**
     * @var BusinessGallery[] - Media Images
     * @ORM\OneToMany(targetEntity="Domain\BusinessBundle\Entity\Media\BusinessGallery",
     *     mappedBy="media",
     *     cascade={"persist"}
     * )
     */
    protected $businessGallery;

    /**
     * @var ArrayCollection - Media Images
     * @ORM\OneToMany(targetEntity="Domain\ArticleBundle\Entity\Media\ArticleGallery",
     *     mappedBy="media",
     *     cascade={"persist"}
     * )
     */
    protected $articleGallery;

    /**
     * @var BusinessProfile[]
     * @ORM\OneToMany(targetEntity="Domain\BusinessBundle\Entity\BusinessProfile",
     *     mappedBy="logo",
     *     cascade={"persist"}
     * )
     */
    protected $logoBusinessProfiles;

    /**
     * @var BusinessProfile[]
     * @ORM\OneToMany(targetEntity="Domain\BusinessBundle\Entity\BusinessProfile",
     *     mappedBy="background",
     *     cascade={"persist"}
     * )
     */
    protected $backgroundBusinessProfiles;

    /**
     * @var BusinessProfile[]
     * @ORM\OneToMany(targetEntity="Domain\PageBundle\Entity\Page",
     *     mappedBy="background",
     *     cascade={"persist"}
     * )
     */
    protected $backgroundPages;

    /**
     * @var Coupon[]
     * @ORM\OneToMany(targetEntity="Domain\BusinessBundle\Entity\Coupon",
     *     mappedBy="image",
     *     cascade={"persist"}
     * )
     */
    protected $coupons;

    /**
     * @var Article[]
     * @ORM\OneToMany(targetEntity="Domain\ArticleBundle\Entity\Article",
     *     mappedBy="image",
     *     cascade={"persist"}
     * )
     */
    protected $articles;

    /**
     * @var VideoMedia[]
     * @ORM\OneToMany(targetEntity="Oxa\VideoBundle\Entity\VideoMedia",
     *     mappedBy="poster",
     *     cascade={"persist"}
     * )
     */
    protected $videoMedia;

    /**
     * @var HomepageCarousel[]
     * @ORM\OneToMany(targetEntity="Domain\BusinessBundle\Entity\HomepageCarousel",
     *     mappedBy="image",
     *     cascade={"persist"}
     * )
     */
    protected $homepageCarousel;

    /**
     * @var PaymentMethod[]
     * @ORM\OneToMany(targetEntity="Domain\BusinessBundle\Entity\PaymentMethod",
     *     mappedBy="image",
     *     cascade={"persist"}
     * )
     */
    protected $paymentMethod;

    /**
     * @var Testimonial[]
     * @ORM\OneToMany(targetEntity="Domain\BusinessBundle\Entity\Testimonial",
     *     mappedBy="image",
     *     cascade={"persist"}
     * )
     */
    protected $testimonials;

    /**
     * Get id
     *
     * @return integer $id
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
     * Sets createdAt.
     *
     * @param  \DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Sets updatedAt.
     *
     * @param  \DateTime $updatedAt
     * @return $this
     */
    public function setUpdatedAt(\DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Available contexts
     *
     * @return array
     */
    public static function getContexts() : array
    {
        return [
            self::CONTEXT_DEFAULT                     => self::CONTEXT_DEFAULT,
            self::CONTEXT_BUSINESS_PROFILE_IMAGES     => self::CONTEXT_BUSINESS_PROFILE_IMAGES,
            self::CONTEXT_BUSINESS_PROFILE_LOGO       => self::CONTEXT_BUSINESS_PROFILE_LOGO,
            self::CONTEXT_BUSINESS_PROFILE_BACKGROUND => self::CONTEXT_BUSINESS_PROFILE_BACKGROUND,
            self::CONTEXT_ARTICLE                     => self::CONTEXT_ARTICLE,
            self::CONTEXT_ARTICLE_IMAGES              => self::CONTEXT_ARTICLE_IMAGES,
            self::CONTEXT_PAGE_BACKGROUND             => self::CONTEXT_PAGE_BACKGROUND,
            self::CONTEXT_VIDEO_POSTER                => self::CONTEXT_VIDEO_POSTER,
            self::CONTEXT_HOMEPAGE_CAROUSEL           => self::CONTEXT_HOMEPAGE_CAROUSEL,
        ];
    }

    /**
     * Available providers
     *
     * @return array
     */
    public static function getProviders() : array
    {
        return [
            self::PROVIDER_IMAGE    => self::PROVIDER_IMAGE,
            self::PROVIDER_FILE     => self::PROVIDER_FILE,
        ];
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->galleryHasMedias = new ArrayCollection();
        $this->backgroundPages  = new ArrayCollection();
        $this->videoMedia       = new ArrayCollection();
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Media
     */
    public function setUrl($url)
    {
        try {
            $this->downloadRemoteFile($url);
        } catch (\Exception $e) {
            //ignore
        }

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
     * Dirty code in entity. Method used to avoid problems with Sonata bundle
     *
     * @access private
     * @param string $url
     * @return void
     */
    private function downloadRemoteFile(string $url)
    {
        $file = file_get_contents($url);

        if ($file) {
            $urlParts = explode('/', $url);
            $fileName = array_pop($urlParts);

            if (!empty($fileName)) {
                $fullFilePath = self::UPLOADS_DIR_NAME . DIRECTORY_SEPARATOR . $fileName;

                file_put_contents($fullFilePath, $file);

                $this->setBinaryContent($fullFilePath);
            }
        }
    }

    /**
     * Add businessGallery
     *
     * @param BusinessGallery $businessGallery
     * @return $this
     */
    public function addBusinessGallery(\Domain\BusinessBundle\Entity\Media\BusinessGallery $businessGallery)
    {
        $this->businessGallery[] = $businessGallery;

        return $this;
    }

    /**
     * Remove businessGallery
     *
     * @param \Domain\BusinessBundle\Entity\Media\BusinessGallery $businessGallery
     */
    public function removeBusinessGallery(\Domain\BusinessBundle\Entity\Media\BusinessGallery $businessGallery)
    {
        $this->businessGallery->removeElement($businessGallery);
    }

    /**
     * Get businessGallery
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBusinessGallery()
    {
        return $this->businessGallery;
    }


    /**
     * Add $articleGallery
     *
     * @param ArticleGallery $articleGallery
     * @return Media $this
     */
    public function addArticleGallery(ArticleGallery $articleGallery)
    {
        $this->articleGallery[] = $articleGallery;

        return $this;
    }

    /**
     * Remove articleGallery
     *
     * @param ArticleGallery $articleGallery
     */
    public function removeArticleGallery(ArticleGallery $articleGallery)
    {
        $this->articleGallery->removeElement($articleGallery);
    }

    /**
     * Get articleGallery
     *
     * @return ArrayCollection
     */
    public function getArticleGallery()
    {
        return $this->articleGallery;
    }

    /**
     * Add businessProfile Logo
     *
     * @param \Domain\BusinessBundle\Entity\BusinessProfile $businessProfile
     *
     * @return Media
     */
    public function addLogoBusinessProfile(\Domain\BusinessBundle\Entity\BusinessProfile $businessProfile)
    {
        $this->logoBusinessProfiles[] = $businessProfile;

        return $this;
    }

    /**
     * Remove businessProfile Logo
     *
     * @param \Domain\BusinessBundle\Entity\BusinessProfile $businessProfile
     */
    public function removeLogoBusinessProfile(\Domain\BusinessBundle\Entity\BusinessProfile $businessProfile)
    {
        $this->logoBusinessProfiles->removeElement($businessProfile);
    }

    /**
     * Get businessProfiles Logo
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLogoBusinessProfiles()
    {
        return $this->logoBusinessProfiles;
    }

    /**
     * Add businessProfile Background
     *
     * @param \Domain\BusinessBundle\Entity\BusinessProfile $businessProfile
     *
     * @return Media
     */
    public function addBackgroundBusinessProfile(\Domain\BusinessBundle\Entity\BusinessProfile $businessProfile)
    {
        $this->backgroundBusinessProfiles[] = $businessProfile;

        return $this;
    }

    /**
     * Remove businessProfile Background
     *
     * @param \Domain\BusinessBundle\Entity\BusinessProfile $businessProfile
     */
    public function removeBackgroundBusinessProfile(\Domain\BusinessBundle\Entity\BusinessProfile $businessProfile)
    {
        $this->backgroundBusinessProfiles->removeElement($businessProfile);
    }

    /**
     * Get businessProfiles Background
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBackgroundBusinessProfiles()
    {
        return $this->backgroundBusinessProfiles;
    }

    /**
     * Add Page Background
     *
     * @param Page $page
     *
     * @return Media
     */
    public function addBackgroundPage(Page $page)
    {
        $this->backgroundPages[] = $page;

        return $this;
    }

    /**
     * Remove page background
     *
     * @param Page $page
     */
    public function removeBackgroundPage(Page $page)
    {
        $this->backgroundPages->removeElement($page);
    }

    /**
     * Get page background
     *
     * @return ArrayCollection
     */
    public function getBackgroundPages()
    {
        return $this->backgroundPages;
    }

    /**
     * Add coupon
     *
     * @param \Domain\BusinessBundle\Entity\Coupon $coupon
     *
     * @return Media
     */
    public function addCoupon(\Domain\BusinessBundle\Entity\Coupon $coupon)
    {
        $this->coupons[] = $coupon;

        return $this;
    }

    /**
     * Remove coupon
     *
     * @param \Domain\BusinessBundle\Entity\Coupon $coupon
     */
    public function removeCoupon(\Domain\BusinessBundle\Entity\Coupon $coupon)
    {
        $this->coupons->removeElement($coupon);
    }

    /**
     * Get coupons
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCoupons()
    {
        return $this->coupons;
    }

    /**
     * Add article
     *
     * @param \Domain\ArticleBundle\Entity\Article $article
     *
     * @return Media
     */
    public function addArticle(\Domain\ArticleBundle\Entity\Article $article)
    {
        $this->articles[] = $article;

        return $this;
    }

    /**
     * Remove article
     *
     * @param \Domain\ArticleBundle\Entity\Article $article
     */
    public function removeArticle(\Domain\ArticleBundle\Entity\Article $article)
    {
        $this->articles->removeElement($article);
    }

    /**
     * Get articles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getArticles()
    {
        return $this->articles;
    }

    /**
     * Add homepageCarousel
     *
     * @param HomepageCarousel $homepageCarousel
     *
     * @return Media
     */
    public function addHomepageCarousel(HomepageCarousel $homepageCarousel)
    {
        $this->homepageCarousel[] = $homepageCarousel;

        return $this;
    }

    /**
     * Remove homepageCarousel
     *
     * @param HomepageCarousel $homepageCarousel
     */
    public function removeHomepageCarousel(HomepageCarousel $homepageCarousel)
    {
        $this->homepageCarousel->removeElement($homepageCarousel);
    }

    /**
     * Get homepageCarousel
     *
     * @return Collection
     */
    public function getHomepageCarousel()
    {
        return $this->homepageCarousel;
    }

    /**
     * Add galleryHasMedia
     *
     * @param \Oxa\Sonata\MediaBundle\Entity\GalleryHasMedia $galleryHasMedia
     *
     * @return Media
     */
    public function addGalleryHasMedia(\Oxa\Sonata\MediaBundle\Entity\GalleryHasMedia $galleryHasMedia)
    {
        $this->galleryHasMedias[] = $galleryHasMedia;

        return $this;
    }

    /**
     * Remove galleryHasMedia
     *
     * @param \Oxa\Sonata\MediaBundle\Entity\GalleryHasMedia $galleryHasMedia
     */
    public function removeGalleryHasMedia(\Oxa\Sonata\MediaBundle\Entity\GalleryHasMedia $galleryHasMedia)
    {
        $this->galleryHasMedias->removeElement($galleryHasMedia);
    }

    /**
     * Add videoMedia poster
     *
     * @param VideoMedia $videoMedia
     *
     * @return Media
     */
    public function addVideoMedia(VideoMedia $videoMedia)
    {
        $this->videoMedia[] = $videoMedia;

        return $this;
    }

    /**
     * Remove videoMedia poster
     *
     * @param VideoMedia $videoMedia
     */
    public function removeVideoMedia(VideoMedia $videoMedia)
    {
        $this->videoMedia->removeElement($videoMedia);
    }

    /**
     * Get videoMedia posters
     *
     * @return ArrayCollection
     */
    public function getVideoMedia()
    {
        return $this->videoMedia;
    }

    /**
     * Add testimonial
     *
     * @param Testimonial $testimonial
     *
     * @return Media
     */
    public function addTestimonial(Testimonial $testimonial)
    {
        $this->testimonials[] = $testimonial;

        return $this;
    }

    /**
     * Remove testimonial
     *
     * @param Testimonial $testimonial
     */
    public function removeTestimonial(Testimonial $testimonial)
    {
        $this->testimonials->removeElement($testimonial);
    }

    /**
     * @param ExecutionContextInterface $context
     */
    public function validateMediaSize(ExecutionContextInterface $context)
    {
        $maxSize = self::getMediaMaxSizeByContext($this->getContext());

        if ($maxSize < $this->getSize()) {
            $context->buildViolation('media.max_size')
                ->setParameter('{{ limit }}', self::getSizeLimitMessage($maxSize))
                ->atPath('binaryContent')
                ->addViolation()
            ;
        }
    }

    /**
     * @param string $context
     *
     * @return int
     */
    public static function getMediaMaxSizeByContext($context)
    {
        switch ($context) {
            case self::CONTEXT_BUSINESS_PROFILE_BACKGROUND:
                $maxSize = self::IMAGE_BACKGROUND_MAX_SIZE;
                break;
            case self::CONTEXT_PAYMENT_METHOD:
                $maxSize = self::IMAGE_PAYMENT_METHOD_MAX_SIZE;
                break;
            default:
                $maxSize = self::IMAGE_MAX_SIZE;
                break;
        }

        return $maxSize;
    }

    /**
     * @param int $sizeInBytes
     *
     * @return string
     */
    public static function getSizeLimitMessage(int $sizeInBytes): string
    {
        if ($sizeInBytes / self::BYTES_IN_KILOBYTE < self::BYTES_IN_KILOBYTE) {
            $limit = $sizeInBytes / self::BYTES_IN_KILOBYTE . ' kb';
        } else {
            $limit = $sizeInBytes / self::BYTES_IN_MEGABYTE . ' Mb';
        }

        return $limit;
    }
}
