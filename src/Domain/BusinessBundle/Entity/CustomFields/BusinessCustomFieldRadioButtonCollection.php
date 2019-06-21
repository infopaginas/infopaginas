<?php

namespace Domain\BusinessBundle\Entity\CustomFields;

use Doctrine\ORM\Mapping as ORM;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\CustomFields\BusinessCustomFieldRadioButton;
use Oxa\Sonata\AdminBundle\Model\ChangeStateInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\ChangeStateTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * BusinessCustomFieldRadioButtonCollection
 *
 * @ORM\Table(name="business_custom_field_radio_button_collection")
 * @ORM\Entity(
 *     repositoryClass="Domain\BusinessBundle\Repository\CustomFields\BusinessCustomFieldRadioButtonCollectionRepository"
 * )
 */
class BusinessCustomFieldRadioButtonCollection implements ChangeStateInterface
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
     *     inversedBy="radioButtonCollection",
     * )
     * @ORM\JoinColumn(name="business_profile_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $businessProfile;

    /**
     * @var BusinessCustomFieldRadioButton[]
     * @Assert\NotBlank()
     *
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\CustomFields\BusinessCustomFieldRadioButton",
     *     inversedBy="radioButtonCollection",
     *     cascade={"persist"}
     *     )
     */
    protected $radioButtons;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $value;

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
     * @return BusinessCustomFieldRadioButtonCollection
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
     * Set businessProfile
     *
     * @param BusinessCustomFieldRadioButton $radioButtonCollection
     *
     * @return BusinessCustomFieldRadioButtonCollection
     */
    public function setRadioButtons(BusinessCustomFieldRadioButton $radioButtonCollection)
    {
        $this->radioButtons = $radioButtonCollection;

        return $this;
    }

    /**
     * Get RadioButtonCollection
     *
     * @return BusinessCustomFieldRadioButton
     */
    public function getRadioButtons()
    {
        return $this->radioButtons;
    }

    /**
     * @param string $value
     *
     * @return BusinessCustomFieldRadioButtonCollection
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
