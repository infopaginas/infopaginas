<?php

namespace Domain\BusinessBundle\Entity\CustomFields;

use Doctrine\ORM\Mapping as ORM;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Oxa\Sonata\AdminBundle\Model\ChangeStateInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\ChangeStateTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * BusinessCustomFieldTextAreaCollection
 *
 * @ORM\Table(name="business_custom_field_text_area_collection")
 * @ORM\Entity(
 *     repositoryClass="Domain\BusinessBundle\Repository\CustomFields\BusinessCustomFieldTextAreaCollectionRepository"
 * )
 */
class BusinessCustomFieldTextAreaCollection implements ChangeStateInterface
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
     *     inversedBy="textAreaCollection",
     * )
     * @ORM\JoinColumn(name="business_profile_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $businessProfile;

    /**
     * @var BusinessCustomFieldTextArea[]
     * @Assert\NotBlank()
     *
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\CustomFields\BusinessCustomFieldTextArea",
     *     inversedBy="textAreaCollection",
     *     cascade={"persist"}
     *     )
     * @ORM\JoinColumn(name="textAreas", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $textAreas;

    /**
     * @var string
     *
     * @ORM\Column(name="textAreaValueEn", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     * @Assert\Length(max=100, min=1)
     */
    protected $textAreaValueEn;

    /**
     * @var string
     *
     * @ORM\Column(name="textAreaValueEs", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     * @Assert\Length(max=100, min=1)
     */
    protected $textAreaValueEs;

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
     * Set textAreaCollection
     *
     * @param BusinessCustomFieldTextArea $textAreaCollection
     *
     * @return BusinessCustomFieldTextAreaCollection
     */
    public function setTextAreas(BusinessCustomFieldTextArea $textAreaCollection)
    {
        $this->textAreas = $textAreaCollection;

        return $this;
    }

    /**
     * Get TextAreas
     *
     * @return BusinessCustomFieldTextArea
     */
    public function getTextAreas()
    {
        return $this->textAreas;
    }

    /**
     * @return string
     */
    public function getTextAreaValueEn()
    {
        return $this->textAreaValueEn;
    }

    /**
     * @param string $textAreaValueEn
     *
     * @return BusinessCustomFieldTextAreaCollection
     */
    public function setTextAreaValueEn($textAreaValueEn)
    {
        $this->textAreaValueEn = $textAreaValueEn;

        return $this;
    }

    /**
     * @return string
     */
    public function getTextAreaValueEs()
    {
        return $this->textAreaValueEs;
    }

    /**
     * @param string $textAreaValueEs
     *
     * @return BusinessCustomFieldTextAreaCollection
     */
    public function setTextAreaValueEs($textAreaValueEs)
    {
        $this->textAreaValueEs = $textAreaValueEs;

        return $this;
    }
}
