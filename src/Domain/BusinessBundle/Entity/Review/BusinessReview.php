<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 5/18/16
 * Time: 5:40 PM
 */

namespace Domain\BusinessBundle\Entity\Review;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Oxa\Sonata\AdminBundle\Model\CopyableEntityInterface;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Gedmo\Mapping\Annotation as Gedmo;
use Oxa\Sonata\UserBundle\Entity\User;
use Sonata\TranslationBundle\Model\Gedmo\TranslatableInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\OxaPersonalTranslatable as PersonalTranslatable;
use Domain\BusinessBundle\Entity\Translation\Review\BusinessReviewTranslation;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * BusinessReview
 *
 * @ORM\Table(name="business_review")
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\BusinessReviewRepository")
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @Gedmo\TranslationEntity(class="Domain\BusinessBundle\Entity\Translation\Review\BusinessReviewTranslation")
 */
class BusinessReview implements DefaultEntityInterface, CopyableEntityInterface, TranslatableInterface
{
    use DefaultEntityTrait;
    use PersonalTranslatable;

    const RATING_MIN_VALUE = 1;
    const RATING_MAX_VALUE = 5;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var User - Business owner
     * @ORM\ManyToOne(targetEntity="Oxa\Sonata\UserBundle\Entity\User",
     *     inversedBy="businessReviews",
     *     cascade={"persist"}
     *     )
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @var string - Name, optional field, which may be used, if a User wants to use a pseudonym
     *
     * @ORM\Column(name="username", type="string", length=100, nullable=true)
     */
    protected $username;

    /**
     * @var string - Profile Rating – 5 mandatory selectable stars
     *
     * @ORM\Column(name="rating", type="integer", nullable=false)
     * @Assert\Range(min = 1, max = 5)
     * @Assert\NotBlank()
     */
    protected $rating;

    /**
     * @var string - Mandatory review text area
     * @ORM\Column(name="content", type="text", length=2000)
     * @Assert\NotBlank()
     */
    protected $content;

    /**
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\BusinessProfile",
     *     inversedBy="businessReviews",
     *     cascade={"persist"}
     *     )
     * @ORM\JoinColumn(name="business_profile_id", referencedColumnName="id")
     */
    protected $businessProfile;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\Translation\Review\BusinessReviewTranslation",
     *     mappedBy="object",
     *     cascade={"persist", "remove"}
     * )
     */
    protected $translations;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->isActive = false;
        $this->translations = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getMarkCopyPropertyName()
    {
        return 'name';
    }

    public function __toString()
    {
        if ($this->getId()) {
            return sprintf('%s: %s', $this->getId(), $this->getBusinessProfile()->__toString());
        } else {
            return 'New Business review';
        }
    }

    /**
     * Get possible rating choices, from Min to Max
     *
     * @return mixed
     */
    public static function getRatingChoices()
    {
        $choices = [];
        for ($i = self::RATING_MIN_VALUE; $i <= self::RATING_MAX_VALUE; $i++) {
            $choices[$i] = $i;
        }

        return $choices;
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
     * Set content
     *
     * @param string $content
     *
     * @return BusinessReview
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set businessProfile
     *
     * @param \Domain\BusinessBundle\Entity\BusinessProfile $businessProfile
     *
     * @return BusinessReview
     */
    public function setBusinessProfile(\Domain\BusinessBundle\Entity\BusinessProfile $businessProfile = null)
    {
        $this->businessProfile = $businessProfile;

        return $this;
    }

    /**
     * Get businessProfile
     *
     * @return \Domain\BusinessBundle\Entity\BusinessProfile
     */
    public function getBusinessProfile()
    {
        return $this->businessProfile;
    }

    /**
     * Set user
     *
     * @param \Oxa\Sonata\UserBundle\Entity\User $user
     *
     * @return BusinessReview
     */
    public function setUser(\Oxa\Sonata\UserBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Oxa\Sonata\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return BusinessReview
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set rating
     *
     * @param integer $rating
     *
     * @return BusinessReview
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return integer
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Remove translation
     *
     * @param \Domain\BusinessBundle\Entity\Translation\Review\BusinessReviewTranslation $translation
     */
    public function removeTranslation(BusinessReviewTranslation $translation)
    {
        $this->translations->removeElement($translation);
    }
}
