<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 5/18/16
 * Time: 5:40 PM
 */

namespace Domain\BusinessBundle\Entity\Review;

use Doctrine\ORM\Mapping as ORM;
use Oxa\Sonata\AdminBundle\Model\CopyableEntityInterface;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Gedmo\Mapping\Annotation as Gedmo;
use Oxa\Sonata\UserBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * BusinessReview
 *
 * @ORM\Table(name="business_review")
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\BusinessReviewRepository")
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class BusinessReview implements DefaultEntityInterface, CopyableEntityInterface
{
    use DefaultEntityTrait;

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
     * @Assert\Range(min = 0, max = 5)
     * @ORM\Column(name="rate", type="integer", nullable=true)
     */
    protected $rate;

    /**
     * @var string - Mandatory review text area
     * @ORM\Column(name="content", type="text", length=2000)
     */
    protected $content;

    /**
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\BusinessProfile",
     *     inversedBy="businessReviews",
     *     cascade={"persist", "remove"}
     *     )
     * @ORM\JoinColumn(name="business_review_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $businessProfile;
    
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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set rate
     *
     * @param integer $rate
     *
     * @return BusinessReview
     */
    public function setRate($rate)
    {
        $this->rate = $rate;

        return $this;
    }

    /**
     * Get rate
     *
     * @return integer
     */
    public function getRate()
    {
        return $this->rate;
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
}
