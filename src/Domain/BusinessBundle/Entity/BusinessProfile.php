<?php

namespace Domain\BusinessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Oxa\Sonata\UserBundle\Entity\User;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * BusinessProfile
 *
 * @ORM\Table(name="business_profile")
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\BusinessProfileRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class BusinessProfile implements DefaultEntityInterface
{
    use DefaultEntityTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string - Business name
     *
     * @ORM\Column(name="name", type="string", length=100)
     */
    private $name;

    /**
     * @var User - Business owner
     * @ORM\ManyToOne(targetEntity="Oxa\Sonata\UserBundle\Entity\User", inversedBy="businessProfiles", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var Subscription - Subscription plan
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\Subscription", inversedBy="businessProfiles", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="subscription_id", referencedColumnName="id", nullable=true)
     */
    private $subscription;

    /**
     * @var Category[] - Business category
     * @ORM\ManyToMany(targetEntity="Domain\BusinessBundle\Entity\Category", inversedBy="businessProfiles", cascade={"persist"})
     * @ORM\JoinTable(name="business_profile_categories")
     */
    private $categories;

    /**
     * @var string - Website
     *
     * @ORM\Column(name="website", type="string", length=30)
     */
    private $website;
    
    /**
     * @var string - Email address
     *
     * @ORM\Column(name="email", type="string", length=30, nullable=true)
     */
    private $email;
    
    /**
     * @var string - Contact phone number
     *
     * @ORM\Column(name="phone", type="string", length=15, nullable=true)
     */
    private $phone;
    
    /**
     * @var \DateTime - Date of registration in Infopaginas
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="registration_date" type="datetime")
     */
    private $registrationDate;

    /**
     * @var Area[] - Using this field a User may define Areas, business is related to.
     * @ORM\ManyToMany(targetEntity="Domain\BusinessBundle\Entity\Area", inversedBy="businessProfiles", cascade={"persist"})
     * @ORM\JoinTable(name="business_profile_areas")
     */
    private $areas;

    /**
     * @var string - Slogan of a Business
     *
     * @ORM\Column(name="slogan", type="string", length=255, nullable=true)
     */
    private $slogan;

    /**
     * @var Tag[] - Tags related to Profile
     * @ORM\ManyToMany(targetEntity="Domain\BusinessBundle\Entity\Tag", inversedBy="businessProfiles", cascade={"persist"})
     * @ORM\JoinTable(name="business_profile_tags")
     */
    private $tags;

    /**
     * @var string - Description of Business
     *
     * @ORM\Column(name="description", type="text", length=1000, nullable=true)
     */
    private $description;

    /**
     * @var string - Products of Business
     *
     * @ORM\Column(name="product", type="text", length=1000, nullable=true)
     */
    private $product;

    /**
     * @var string - Operational Hours
     *
     * @ORM\Column(name="working_hours", type="string", length=255, nullable=true)
     */
    private $workingHours;

    /**
     * @var Brand[] - Brands, Business works with
     * @ORM\ManyToMany(targetEntity="Domain\BusinessBundle\Entity\Brand", inversedBy="businessProfiles", cascade={"persist"})
     * @ORM\JoinTable(name="business_profile_brands")
     */
    private $brands;

    /**
     * @var PaymentMethod[] - Contains list of Payment Methods
     * @ORM\ManyToMany(targetEntity="Domain\BusinessBundle\Entity\PaymentMethod", inversedBy="businessProfiles", cascade={"persist"})
     * @ORM\JoinTable(name="business_profile_payment_methods")
     */
    private $paymentMethods;

    /**
     * @var string - Field is checked, if Description field of profile is set.
     *
     * @ORM\Column(name="is_set_description", type="boolean", options={"default" : 0})
     */
    private $isSetDescription;

    /**
     * @var string - Field is checked, if business is marked on map.
     *
     * @ORM\Column(name="is_set_map", type="boolean", options={"default" : 0})
     */
    private $isSetMap;

    /**
     * @var string - Field is checked, if Ad is defined.
     *
     * @ORM\Column(name="", type="boolean", options={"default" : 0})
     */
    private $isSetAd;

    /**
     * @var string - Field is checked, if Logo field of profile is set.
     *
     * @ORM\Column(name="", type="boolean", options={"default" : 0})
     */
    private $isSetLogo;

    /**
     * @var string - Field is checked, if Slogan field of profile is set.
     *
     * @ORM\Column(name="", type="boolean", options={"default" : 0})
     */
    private $isSetSlogan;

    /**
     * @var string - Used to create human like url
     *
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(name="slug", type="string", length=100)
     */
    private $slug;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}

