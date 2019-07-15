<?php

namespace Domain\BusinessBundle\Entity\CustomFields;

use Doctrine\ORM\Mapping as ORM;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\CustomFields\BusinessCustomFieldCheckbox;
use Oxa\Sonata\AdminBundle\Model\ChangeStateInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\ChangeStateTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * BusinessCustomFieldCheckboxCollection
 *
 * @ORM\Table(name="business_custom_field_checkbox_collection")
 * @ORM\Entity(
 *     repositoryClass="Domain\BusinessBundle\Repository\CustomFields\BusinessCustomFieldCheckboxCollectionRepository"
 * )
 */
class BusinessCustomFieldCheckboxCollection implements ChangeStateInterface
{
    use ChangeStateTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var BusinessProfile
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\BusinessProfile",
     *     cascade={"persist"},
     *     inversedBy="checkboxCollection",
     * )
     * @ORM\JoinColumn(name="business_profile_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $businessProfile;

    /**
     * @var BusinessCustomFieldCheckbox[]
     * @Assert\NotBlank()
     *
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\CustomFields\BusinessCustomFieldCheckbox",
     *     inversedBy="checkboxCollection",
     *     cascade={"persist"}
     *     )
     */
    protected $checkboxes;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_available", type="boolean")
     */
    private $isAvailable;

    /**
     * @return string
     */
    public function __toString()
    {
        return 'collection';
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
     * Set businessProfile
     *
     * @param BusinessProfile $businessProfile
     *
     * @return BusinessCustomFieldCheckboxCollection
     */
    public function setBusinessProfile(BusinessProfile $businessProfile = null)
    {
        $this->businessProfile = $businessProfile;

        return $this;
    }

    /**
     * Get businessProfile
     *
     * @return BusinessProfile
     */
    public function getBusinessProfile()
    {
        return $this->businessProfile;
    }

    /**
     * Set CheckboxCollection
     *
     * @param BusinessCustomFieldCheckbox $checkboxCollection
     *
     * @return BusinessCustomFieldCheckboxCollection
     */
    public function setCheckboxes(BusinessCustomFieldCheckbox $checkboxCollection)
    {
        $this->checkboxes = $checkboxCollection;

        return $this;
    }

    /**
     * Get CheckboxCollection
     *
     * @return BusinessCustomFieldCheckbox
     */
    public function getCheckboxes()
    {
        return $this->checkboxes;
    }

    /**
     * @param boolean $isAvailable
     *
     * @return BusinessCustomFieldCheckboxCollection
     */
    public function setIsAvailable($isAvailable)
    {
        $this->isAvailable = $isAvailable;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getIsAvailable()
    {
        return $this->isAvailable;
    }
}
