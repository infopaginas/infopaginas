<?php

namespace Oxa\Sonata\MediaBundle\Entity;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Coupon;
use Domain\ArticleBundle\Entity\Article;
use Domain\BusinessBundle\Entity\Media\BusinessGallery;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Oxa\Sonata\MediaBundle\Model\OxaMediaInterface;
use Sonata\MediaBundle\Entity\BaseMedia as BaseMedia;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="media__media")
 * @ORM\Entity(repositoryClass="Oxa\Sonata\MediaBundle\Repository\MediaRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Media extends BaseMedia implements OxaMediaInterface, DefaultEntityInterface
{
    use DefaultEntityTrait;

    const UPLOADS_DIR_NAME = 'uploads';

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
            self::CONTEXT_DEFAULT                       => self::CONTEXT_DEFAULT,
            self::CONTEXT_BUSINESS_PROFILE_IMAGES       => self::CONTEXT_BUSINESS_PROFILE_IMAGES,
            self::CONTEXT_BUSINESS_PROFILE_LOGO         => self::CONTEXT_BUSINESS_PROFILE_LOGO,
            self::CONTEXT_BUSINESS_PROFILE_BACKGROUND   => self::CONTEXT_BUSINESS_PROFILE_BACKGROUND,
            self::CONTEXT_BANNER                        => self::CONTEXT_BANNER,
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
        $this->galleryHasMedias = new \Doctrine\Common\Collections\ArrayCollection();
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
}
