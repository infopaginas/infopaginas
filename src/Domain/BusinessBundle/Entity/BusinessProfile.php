<?php

namespace Domain\BusinessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Domain\BusinessBundle\Entity\Review\BusinessReview;
use Domain\BusinessBundle\Entity\Task\Task;
use Oxa\Sonata\AdminBundle\Model\CopyableEntityInterface;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Oxa\Sonata\UserBundle\Entity\User;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * BusinessProfile
 *
 * @ORM\Table(name="business_profile")
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\BusinessProfileRepository")
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class BusinessProfile implements DefaultEntityInterface, CopyableEntityInterface
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
     * @var string - Business name
     *
     * @ORM\Column(name="name", type="string", length=100)
     */
    protected $name;

    /**
     * @var User - Business owner
     * @ORM\ManyToOne(targetEntity="Oxa\Sonata\UserBundle\Entity\User", 
     *     inversedBy="businessProfiles", 
     *     cascade={"persist"}
     *     )
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @var Subscription - Subscription plan
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\Subscription", 
     *     inversedBy="businessProfiles", 
     *     cascade={"persist", "remove"}
     *     )
     * @ORM\JoinColumn(name="subscription_id", referencedColumnName="id", nullable=true)
     */
    protected $subscription;

    /**
     * @var Category[] - Business category
     * @ORM\ManyToMany(targetEntity="Domain\BusinessBundle\Entity\Category", 
     *     inversedBy="businessProfiles", 
     *     cascade={"persist"}
     *     )
     * @ORM\JoinTable(name="business_profile_categories")
     */
    protected $categories;

    /**
     * @var string - Website
     *
     * @ORM\Column(name="website", type="string", length=30)
     */
    protected $website;
    
    /**
     * @var string - Email address
     *
     * @ORM\Column(name="email", type="string", length=30, nullable=true)
     */
    protected $email;
    
    /**
     * @var string - Contact phone number
     *
     * @ORM\Column(name="phone", type="string", length=15, nullable=true)
     */
    protected $phone;
    
    /**
     * @var \DateTime - Date of registration in Infopaginas
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="registration_date", type="datetime")
     */
    protected $registrationDate;

    /**
     * @var Area[] - Using this field a User may define Areas, business is related to.
     * @ORM\ManyToMany(targetEntity="Domain\BusinessBundle\Entity\Area", 
     *     inversedBy="businessProfiles"
     * )
     * @ORM\JoinTable(name="business_profile_areas")
     */
    protected $areas;

    /**
     * @var string - Slogan of a Business
     *
     * @ORM\Column(name="slogan", type="string", length=255, nullable=true)
     */
    protected $slogan;

    /**
     * @var Tag[] - Tags related to Profile
     * @ORM\ManyToMany(targetEntity="Domain\BusinessBundle\Entity\Tag", 
     *     inversedBy="businessProfiles", 
     *     cascade={"persist"}
     *     )
     * @ORM\JoinTable(name="business_profile_tags")
     */
    protected $tags;

    /**
     * @var string - Description of Business
     *
     * @ORM\Column(name="description", type="text", length=1000, nullable=true)
     */
    protected $description;

    /**
     * @var string - Products of Business
     *
     * @ORM\Column(name="product", type="text", length=1000, nullable=true)
     */
    protected $product;

    /**
     * @var string - Operational Hours
     *
     * @ORM\Column(name="working_hours", type="string", length=255, nullable=true)
     */
    protected $workingHours;

    /**
     * @var Brand[] - Brands, Business works with
     * @ORM\ManyToMany(targetEntity="Domain\BusinessBundle\Entity\Brand", 
     *     inversedBy="businessProfiles", 
     *     cascade={"persist"}
     *     )
     * @ORM\JoinTable(name="business_profile_brands")
     */
    protected $brands;

    /**
     * @var PaymentMethod[] - Contains list of Payment Methods
     * @ORM\ManyToMany(targetEntity="Domain\BusinessBundle\Entity\PaymentMethod", 
     *     inversedBy="businessProfiles", 
     *     cascade={"persist"}
     *     )
     * @ORM\JoinTable(name="business_profile_payment_methods")
     */
    protected $paymentMethods;

    /**
     * @var string - Field is checked, if Description field of profile is set.
     *
     * @ORM\Column(name="is_set_description", type="boolean", options={"default" : 0})
     */
    protected $isSetDescription = false;

    /**
     * @var string - Field is checked, if business is marked on map.
     *
     * @ORM\Column(name="is_set_map", type="boolean", options={"default" : 0})
     */
    protected $isSetMap = false;

    /**
     * @var string - Field is checked, if Ad is defined.
     *
     * @ORM\Column(name="is_set_ad", type="boolean", options={"default" : 0})
     */
    protected $isSetAd = false;

    /**
     * @var string - Field is checked, if Logo field of profile is set.
     *
     * @ORM\Column(name="is_set_logo", type="boolean", options={"default" : 0})
     */
    protected $isSetLogo = false;

    /**
     * @var string - Field is checked, if Slogan field of profile is set.
     *
     * @ORM\Column(name="is_set_slogan", type="boolean", options={"default" : 0})
     */
    protected $isSetSlogan = false;

    /**
     * @var string - Used to create human like url
     *
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(name="slug", type="string", length=100)
     */
    protected $slug;

    /**
     * @var Task[]
     * @ORM\OneToMany(targetEntity="Domain\BusinessBundle\Entity\Task", mappedBy="businessProfile")
     */
    protected $tasks;

    /**
     * @var BusinessReview[]
     * @ORM\OneToMany(targetEntity="Domain\BusinessBundle\Entity\Review\BusinessReview",
     *     mappedBy="businessProfile",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     *     )
     */
    protected $businessReviews;

    public function getMarkCopyPropertyName()
    {
        return 'name';
    }

    public function __toString()
    {
        return ($this->getName()) ?: 'New business';
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
     * Constructor
     */
    public function __construct()
    {
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
        $this->areas = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tags = new \Doctrine\Common\Collections\ArrayCollection();
        $this->brands = new \Doctrine\Common\Collections\ArrayCollection();
        $this->paymentMethods = new \Doctrine\Common\Collections\ArrayCollection();
        $this->businessReviews = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return BusinessProfile
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
     * Set website
     *
     * @param string $website
     *
     * @return BusinessProfile
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get website
     *
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return BusinessProfile
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return BusinessProfile
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set registrationDate
     *
     * @param \DateTime $registrationDate
     *
     * @return BusinessProfile
     */
    public function setRegistrationDate($registrationDate)
    {
        $this->registrationDate = $registrationDate;

        return $this;
    }

    /**
     * Get registrationDate
     *
     * @return \DateTime
     */
    public function getRegistrationDate()
    {
        return $this->registrationDate;
    }

    /**
     * Set slogan
     *
     * @param string $slogan
     *
     * @return BusinessProfile
     */
    public function setSlogan($slogan)
    {
        $this->slogan = $slogan;

        return $this;
    }

    /**
     * Get slogan
     *
     * @return string
     */
    public function getSlogan()
    {
        return $this->slogan;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return BusinessProfile
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
     * Set product
     *
     * @param string $product
     *
     * @return BusinessProfile
     */
    public function setProduct($product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return string
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set workingHours
     *
     * @param string $workingHours
     *
     * @return BusinessProfile
     */
    public function setWorkingHours($workingHours)
    {
        $this->workingHours = $workingHours;

        return $this;
    }

    /**
     * Get workingHours
     *
     * @return string
     */
    public function getWorkingHours()
    {
        return $this->workingHours;
    }

    /**
     * Set isSetDescription
     *
     * @param boolean $isSetDescription
     *
     * @return BusinessProfile
     */
    public function setIsSetDescription($isSetDescription)
    {
        $this->isSetDescription = $isSetDescription;

        return $this;
    }

    /**
     * Get isSetDescription
     *
     * @return boolean
     */
    public function getIsSetDescription()
    {
        return $this->isSetDescription;
    }

    /**
     * Set isSetMap
     *
     * @param boolean $isSetMap
     *
     * @return BusinessProfile
     */
    public function setIsSetMap($isSetMap)
    {
        $this->isSetMap = $isSetMap;

        return $this;
    }

    /**
     * Get isSetMap
     *
     * @return boolean
     */
    public function getIsSetMap()
    {
        return $this->isSetMap;
    }

    /**
     * Set isSetAd
     *
     * @param boolean $isSetAd
     *
     * @return BusinessProfile
     */
    public function setIsSetAd($isSetAd)
    {
        $this->isSetAd = $isSetAd;

        return $this;
    }

    /**
     * Get isSetAd
     *
     * @return boolean
     */
    public function getIsSetAd()
    {
        return $this->isSetAd;
    }

    /**
     * Set isSetLogo
     *
     * @param boolean $isSetLogo
     *
     * @return BusinessProfile
     */
    public function setIsSetLogo($isSetLogo)
    {
        $this->isSetLogo = $isSetLogo;

        return $this;
    }

    /**
     * Get isSetLogo
     *
     * @return boolean
     */
    public function getIsSetLogo()
    {
        return $this->isSetLogo;
    }

    /**
     * Set isSetSlogan
     *
     * @param boolean $isSetSlogan
     *
     * @return BusinessProfile
     */
    public function setIsSetSlogan($isSetSlogan)
    {
        $this->isSetSlogan = $isSetSlogan;

        return $this;
    }

    /**
     * Get isSetSlogan
     *
     * @return boolean
     */
    public function getIsSetSlogan()
    {
        return $this->isSetSlogan;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return BusinessProfile
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
     * Set user
     *
     * @param \Oxa\Sonata\UserBundle\Entity\User $user
     *
     * @return BusinessProfile
     */
    public function setUser(\Oxa\Sonata\UserBundle\Entity\User $user = null)
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
     * Set subscription
     *
     * @param \Domain\BusinessBundle\Entity\Subscription $subscription
     *
     * @return BusinessProfile
     */
    public function setSubscription(\Domain\BusinessBundle\Entity\Subscription $subscription = null)
    {
        $this->subscription = $subscription;

        return $this;
    }

    /**
     * Get subscription
     *
     * @return \Domain\BusinessBundle\Entity\Subscription
     */
    public function getSubscription()
    {
        return $this->subscription;
    }

    /**
     * Add category
     *
     * @param \Domain\BusinessBundle\Entity\Category $category
     *
     * @return BusinessProfile
     */
    public function addCategory(\Domain\BusinessBundle\Entity\Category $category)
    {
        $this->categories[] = $category;

        return $this;
    }

    /**
     * Remove category
     *
     * @param \Domain\BusinessBundle\Entity\Category $category
     */
    public function removeCategory(\Domain\BusinessBundle\Entity\Category $category)
    {
        $this->categories->removeElement($category);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Add area
     *
     * @param \Domain\BusinessBundle\Entity\Area $area
     *
     * @return BusinessProfile
     */
    public function addArea(\Domain\BusinessBundle\Entity\Area $area)
    {
        $this->areas[] = $area;

        return $this;
    }

    /**
     * Remove area
     *
     * @param \Domain\BusinessBundle\Entity\Area $area
     */
    public function removeArea(\Domain\BusinessBundle\Entity\Area $area)
    {
        $this->areas->removeElement($area);
    }

    /**
     * Get areas
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAreas()
    {
        return $this->areas;
    }

    /**
     * Add tag
     *
     * @param \Domain\BusinessBundle\Entity\Tag $tag
     *
     * @return BusinessProfile
     */
    public function addTag(\Domain\BusinessBundle\Entity\Tag $tag)
    {
        $this->tags[] = $tag;

        return $this;
    }

    /**
     * Remove tag
     *
     * @param \Domain\BusinessBundle\Entity\Tag $tag
     */
    public function removeTag(\Domain\BusinessBundle\Entity\Tag $tag)
    {
        $this->tags->removeElement($tag);
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Add brand
     *
     * @param \Domain\BusinessBundle\Entity\Brand $brand
     *
     * @return BusinessProfile
     */
    public function addBrand(\Domain\BusinessBundle\Entity\Brand $brand)
    {
        $this->brands[] = $brand;

        return $this;
    }

    /**
     * Remove brand
     *
     * @param \Domain\BusinessBundle\Entity\Brand $brand
     */
    public function removeBrand(\Domain\BusinessBundle\Entity\Brand $brand)
    {
        $this->brands->removeElement($brand);
    }

    /**
     * Get brands
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBrands()
    {
        return $this->brands;
    }

    /**
     * Add paymentMethod
     *
     * @param \Domain\BusinessBundle\Entity\PaymentMethod $paymentMethod
     *
     * @return BusinessProfile
     */
    public function addPaymentMethod(\Domain\BusinessBundle\Entity\PaymentMethod $paymentMethod)
    {
        $this->paymentMethods[] = $paymentMethod;

        return $this;
    }

    /**
     * Remove paymentMethod
     *
     * @param \Domain\BusinessBundle\Entity\PaymentMethod $paymentMethod
     */
    public function removePaymentMethod(\Domain\BusinessBundle\Entity\PaymentMethod $paymentMethod)
    {
        $this->paymentMethods->removeElement($paymentMethod);
    }

    /**
     * Get paymentMethods
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPaymentMethods()
    {
        return $this->paymentMethods;
    }

    /**
     * Add task
     *
     * @param \Domain\BusinessBundle\Entity\Task\Task $task
     *
     * @return BusinessProfile
     */
    public function addTask(\Domain\BusinessBundle\Entity\Task\Task $task)
    {
        $this->tasks[] = $task;

        return $this;
    }

    /**
     * Remove task
     *
     * @param \Domain\BusinessBundle\Entity\Task\Task $task
     */
    public function removeTask(\Domain\BusinessBundle\Entity\Task\Task $task)
    {
        $this->tasks->removeElement($task);
    }

    /**
     * Get tasks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    /**
     * Add businessReview
     *
     * @param \Domain\BusinessBundle\Entity\Review\BusinessReview $businessReview
     *
     * @return BusinessProfile
     */
    public function addBusinessReview(\Domain\BusinessBundle\Entity\Review\BusinessReview $businessReview)
    {
        $this->businessReviews[] = $businessReview;

        return $this;
    }

    /**
     * Remove businessReview
     *
     * @param \Domain\BusinessBundle\Entity\Review\BusinessReview $businessReview
     */
    public function removeBusinessReview(\Domain\BusinessBundle\Entity\Review\BusinessReview $businessReview)
    {
        $this->businessReviews->removeElement($businessReview);
    }

    /**
     * Get businessReviews
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBusinessReviews()
    {
        return $this->businessReviews;
    }
}
