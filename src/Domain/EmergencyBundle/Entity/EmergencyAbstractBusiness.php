<?php

namespace Domain\EmergencyBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Oxa\GeolocationBundle\Model\Geolocation\GeolocationInterface;
use Oxa\GeolocationBundle\Utils\Traits\LocationTrait;
use Symfony\Component\Validator\Constraints as Assert;

class EmergencyAbstractBusiness  implements GeolocationInterface
{
    use TimestampableEntity;
    use LocationTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string - Business name
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\Length(max=255, maxMessage="business_profile.max_length")
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     * @Assert\Length(max=255, maxMessage="business_profile.max_length")
     * @Assert\NotBlank()
     */
    protected $address;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=15, nullable=true)
     * @Assert\Length(max=15)
     * @Assert\NotBlank()
     */
    protected $phone;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", options={"default" : 0})
     */
    protected $isActive;

    /**
     * @var EmergencyCategory|null $category
     */
    protected $category;

    /**
     * @var EmergencyArea
     */
    protected $area;

    /**
     * @var \Domain\BusinessBundle\Entity\PaymentMethod[]
     */
    protected $paymentMethods;

    /**
     * @var EmergencyService[]
     */
    protected $services;

    /**
     * @var ArrayCollection
     */
    protected $collectionWorkingHours;

    /**
     * @var string
     *
     * @ORM\Column(name="working_hours_json", type="text", nullable=true)
     */
    protected $workingHoursJson;

    /**
     * @var boolean
     *
     * @ORM\Column(name="use_map_address", type="boolean", options={"default" : 0})
     */
    protected $useMapAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="google_address", type="string", nullable=true)
     */
    protected $googleAddress;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->paymentMethods = new ArrayCollection();
        $this->services       = new ArrayCollection();
        $this->collectionWorkingHours = new ArrayCollection();

        $this->isActive       = true;
        $this->useMapAddress  = false;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName() ?: '';
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
     * Set name
     *
     * @param string $name
     *
     * @return EmergencyBusiness
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
     * Set address
     *
     * @param string $address
     *
     * @return EmergencyBusiness
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $phone
     *
     * @return EmergencyBusiness
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param boolean $isActive
     *
     * @return EmergencyBusiness
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set $area
     *
     * @param EmergencyArea|null $area
     *
     * @return EmergencyBusiness
     */
    public function setArea(EmergencyArea $area = null)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * Get area
     *
     * @return EmergencyArea|null
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * Set category
     *
     * @param EmergencyCategory|null $category
     *
     * @return EmergencyBusiness
     */
    public function setCategory(EmergencyCategory $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return EmergencyCategory|null
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Add paymentMethod
     *
     * @param \Domain\BusinessBundle\Entity\PaymentMethod $paymentMethod
     *
     * @return EmergencyBusiness
     */
    public function addPaymentMethod(\Domain\BusinessBundle\Entity\PaymentMethod $paymentMethod)
    {
        $this->paymentMethods->add($paymentMethod);

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
     * Add paymentMethod
     *
     * @param EmergencyService $service
     *
     * @return EmergencyBusiness
     */
    public function addService(EmergencyService $service)
    {
        $this->services->add($service);

        return $this;
    }

    /**
     * Remove service
     *
     * @param EmergencyService $service
     */
    public function removeService(EmergencyService $service)
    {
        $this->services->removeElement($service);
    }

    /**
     * Get services
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * Add $workingHour
     *
     * @param EmergencyBusinessWorkingHour $workingHour
     *
     * @return EmergencyBusiness
     */
    public function addCollectionWorkingHour($workingHour)
    {
        $this->collectionWorkingHours->add($workingHour);
        $workingHour->setBusiness($this);

        return $this;
    }

    /**
     * Remove $workingHours
     *
     * @param EmergencyBusinessWorkingHour $workingHours
     */
    public function removeCollectionWorkingHour(EmergencyBusinessWorkingHour $workingHours)
    {
        $this->collectionWorkingHours->removeElement($workingHours);
    }

    /**
     * @return ArrayCollection
     */
    public function getCollectionWorkingHours()
    {
        return $this->collectionWorkingHours;
    }

    /**
     * @return string
     */
    public function getWorkingHoursJson()
    {
        return $this->workingHoursJson;
    }

    /**
     * @param string $workingHoursJson
     *
     * @return EmergencyBusiness
     */
    public function setWorkingHoursJson($workingHoursJson)
    {
        $this->workingHoursJson = $workingHoursJson;

        return $this;
    }

    /**
     * @return \stdClass
     */
    public function getWorkingHoursJsonAsObject()
    {
        return json_decode($this->getWorkingHoursJson());
    }

    /**
     * @param string $googleAddress
     *
     * @return EmergencyAbstractBusiness
     */
    public function setGoogleAddress($googleAddress)
    {
        $this->googleAddress = $googleAddress;

        return $this;
    }

    /**
     * @return string
     */
    public function getGoogleAddress()
    {
        return $this->googleAddress;
    }

    /**
     * @param boolean $useMapAddress
     *
     * @return EmergencyAbstractBusiness
     */
    public function setUseMapAddress($useMapAddress)
    {
        $this->useMapAddress = $useMapAddress;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getUseMapAddress()
    {
        return $this->useMapAddress;
    }
}
