<?php

namespace Domain\EmergencyBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Domain\BusinessBundle\Validator\Constraints\BusinessProfileWorkingHourType as BusinessWorkingHourTypeValidator;
use Domain\EmergencyBundle\Validator\Constraints\EmergencyDraftBusinessCategoryType as DraftCategoryTypeValidator;

/**
 * EmergencyBusiness
 *
 * @ORM\Table(name="emergency_draft_business")
 * @ORM\Entity(repositoryClass="Domain\EmergencyBundle\Repository\EmergencyDraftBusinessRepository")
 * @ORM\HasLifecycleCallbacks
 * @BusinessWorkingHourTypeValidator()
 * @DraftCategoryTypeValidator()
 */
class EmergencyDraftBusiness extends EmergencyAbstractBusiness
{
    const STATUS_PENDING  = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    const FIELD_CATEGORY = 'category';
    const FIELD_CUSTOM_CATEGORY = 'customCategory';

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=15, options={"default": EmergencyDraftBusiness::STATUS_PENDING})
     * @Assert\Choice(callback = "getStatusAssert", multiple = false)
     */
    protected $status;

    /**
     * @var string - Custom category name
     *
     * @ORM\Column(name="custom_category", type="string", length=255, nullable=true)
     * @Assert\Length(max="255")
     */
    protected $customCategory;

    /**
     * Related to FIELD_CUSTOM_CATEGORY const
     * @var string - Custom working hours
     *
     * @ORM\Column(name="custom_working_hours", type="string", length=255, nullable=true)
     * @Assert\Length(max="255")
     */
    protected $customWorkingHours;

    /**
     * Related to FIELD_CATEGORY const
     * @var EmergencyCategory|null $category
     * @ORM\ManyToOne(targetEntity="Domain\EmergencyBundle\Entity\EmergencyCategory",
     *     inversedBy="draftBusinesses",
     *     cascade={"persist"}
     *     )
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=true)
     */
    protected $category;

    /**
     * @var EmergencyArea|null $area
     * @ORM\ManyToOne(targetEntity="Domain\EmergencyBundle\Entity\EmergencyArea",
     *     inversedBy="draftBusinesses",
     *     cascade={"persist"}
     *     )
     * @ORM\JoinColumn(name="area_id", referencedColumnName="id", nullable=true)
     * @Assert\NotBlank()
     */
    protected $area;

    /**
     * @var \Domain\BusinessBundle\Entity\PaymentMethod[] - Contains list of Payment Methods
     * @ORM\ManyToMany(targetEntity="Domain\BusinessBundle\Entity\PaymentMethod",
     *     inversedBy="emergencyDraftBusinesses",
     *     cascade={"persist"}
     *     )
     * @ORM\JoinTable(name="emergency_draft_business_payment_methods")
     */
    protected $paymentMethods;

    /**
     * @var EmergencyService[] - Contains list of Services
     * @ORM\ManyToMany(targetEntity="Domain\EmergencyBundle\Entity\EmergencyService",
     *     inversedBy="draftBusinesses",
     *     cascade={"persist"}
     *     )
     * @ORM\JoinTable(name="emergency_draft_business_services")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    protected $services;

    /**
     * @var ArrayCollection - Business Profile working hours
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\EmergencyBundle\Entity\EmergencyBusinessWorkingHour",
     *     mappedBy="draftBusiness",
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

        $this->status = self::STATUS_PENDING;
    }

    /**
     * @param string $customCategory
     *
     * @return EmergencyDraftBusiness
     */
    public function setCustomCategory($customCategory)
    {
        $this->customCategory = $customCategory;

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomCategory()
    {
        return $this->customCategory;
    }

    /**
     * @param string $customWorkingHours
     *
     * @return EmergencyDraftBusiness
     */
    public function setCustomWorkingHours($customWorkingHours)
    {
        $this->customWorkingHours = $customWorkingHours;

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomWorkingHours()
    {
        return $this->customWorkingHours;
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
        $workingHour->setDraftBusiness($this);

        return $this;
    }

    /**
     * @param string $status
     *
     * @return EmergencyDraftBusiness
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return array
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING  => 'emergency.business_draft.status_pending',
            self::STATUS_APPROVED => 'emergency.business_draft.status_approved',
            self::STATUS_REJECTED => 'emergency.business_draft.status_rejected',
        ];
    }

    /**
     * @return array
     */
    public static function getStatusAssert()
    {
        return array_keys(self::getStatuses());
    }
}
