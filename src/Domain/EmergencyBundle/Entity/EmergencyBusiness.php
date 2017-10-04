<?php

namespace Domain\EmergencyBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
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
class EmergencyBusiness extends EmergencyAbstractBusiness
{
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
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->isActive       = true;
    }
}
