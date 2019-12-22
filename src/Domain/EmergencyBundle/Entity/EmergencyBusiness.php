<?php

namespace Domain\EmergencyBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Domain\ReportBundle\Model\ReportInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use Oxa\Sonata\AdminBundle\Model\ChangeStateInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\ChangeStateTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Domain\BusinessBundle\Validator\Constraints\BusinessProfileWorkingHourType as BusinessWorkingHourTypeValidator;

/**
 * EmergencyBusiness
 *
 * @ORM\Table(name="emergency_business")
 * @ORM\Entity(repositoryClass="Domain\EmergencyBundle\Repository\EmergencyBusinessRepository")
 * @ORM\HasLifecycleCallbacks
 * @BusinessWorkingHourTypeValidator()
 */
class EmergencyBusiness extends EmergencyAbstractBusiness implements ReportInterface, ChangeStateInterface
{
    use ChangeStateTrait;

    const ELASTIC_DOCUMENT_TYPE = 'EmergencyBusiness';
    const ELASTIC_INDEX = 'emergency_business';
    const DISTANCE_TO_BUSINESS_PRECISION = 1;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_updated", type="boolean", options={"default" : 0})
     */
    protected $isUpdated;

    /**
     * @var string - Business first symbol filter
     *
     * @ORM\Column(name="first_symbol", type="string", length=10, nullable=true)
     */
    protected $firstSymbol;

    /**
     * @var EmergencyCategory|null $category
     * @ORM\ManyToOne(targetEntity="Domain\EmergencyBundle\Entity\EmergencyCategory",
     *     inversedBy="businesses",
     *     cascade={"persist"}
     *     )
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=true)
     * @Assert\NotBlank()
     */
    protected $category;

    /**
     * @var EmergencyArea|null $area
     * @ORM\ManyToOne(targetEntity="Domain\EmergencyBundle\Entity\EmergencyArea",
     *     inversedBy="businesses",
     *     cascade={"persist"}
     *     )
     * @ORM\JoinColumn(name="area_id", referencedColumnName="id", nullable=true)
     * @Assert\NotBlank()
     */
    protected $area;

    /**
     * @var \Domain\BusinessBundle\Entity\PaymentMethod[] - Contains list of Payment Methods
     * @ORM\ManyToMany(targetEntity="Domain\BusinessBundle\Entity\PaymentMethod",
     *     inversedBy="emergencyBusinesses",
     *     cascade={"persist"}
     *     )
     * @ORM\JoinTable(name="emergency_business_payment_methods")
     */
    protected $paymentMethods;

    /**
     * @var EmergencyService[] - Contains list of Services
     * @ORM\ManyToMany(targetEntity="Domain\EmergencyBundle\Entity\EmergencyService",
     *     inversedBy="businesses",
     *     cascade={"persist"}
     *     )
     * @ORM\JoinTable(name="emergency_business_services")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    protected $services;

    /**
     * @var ArrayCollection - Business Profile working hours
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\EmergencyBundle\Entity\EmergencyBusinessWorkingHour",
     *     mappedBy="business",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     * )
     * @Assert\Valid()
     */
    protected $collectionWorkingHours;

    /**
     * @var float
     *
     * Distance to the user
     */
    protected $distance;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->isActive       = true;
        $this->isUpdated      = true;
    }

    /**
     * @param boolean $isUpdated
     *
     * @return EmergencyBusiness
     */
    public function setIsUpdated($isUpdated)
    {
        $this->isUpdated = $isUpdated;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getIsUpdated()
    {
        return $this->isUpdated;
    }

    /**
     * @param string $firstSymbol
     *
     * @return EmergencyBusiness
     */
    public function setFirstSymbol($firstSymbol)
    {
        $this->firstSymbol = $firstSymbol;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstSymbol()
    {
        return $this->firstSymbol;
    }

    /**
     * @param float|null $distance
     *
     * @return EmergencyBusiness
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * Get formatted distance
     *
     * @return string
     */
    public function getDistanceUX()
    {
        return number_format($this->getDistance(), self::DISTANCE_TO_BUSINESS_PRECISION, '.', '');
    }
}
