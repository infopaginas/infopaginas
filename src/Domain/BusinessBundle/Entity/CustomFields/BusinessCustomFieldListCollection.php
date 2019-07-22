<?php

namespace Domain\BusinessBundle\Entity\CustomFields;

use Doctrine\ORM\Mapping as ORM;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Oxa\Sonata\AdminBundle\Model\ChangeStateInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\ChangeStateTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * BusinessCustomFieldListCollection
 *
 * @ORM\Table(name="business_custom_field_list_collection")
 * @ORM\Entity(
 *     repositoryClass="Domain\BusinessBundle\Repository\CustomFields\BusinessCustomFieldListCollectionRepository"
 * )
 */
class BusinessCustomFieldListCollection implements ChangeStateInterface
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
     *     inversedBy="listCollection",
     * )
     * @ORM\JoinColumn(name="business_profile_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $businessProfile;

    /**
     * @var BusinessCustomFieldList[]
     * @Assert\NotBlank()
     *
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\CustomFields\BusinessCustomFieldList",
     *     inversedBy="listCollection",
     *     cascade={"persist"}
     * )
     * @ORM\JoinColumn(name="lists", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $lists;

    /**
     * @var BusinessCustomFieldListItem
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\CustomFields\BusinessCustomFieldListItem",
     *     cascade={"persist"},
     * )
     * @ORM\JoinColumn(name="value", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $value;

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
     * @return BusinessCustomFieldListCollection
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
     * Set listCollection
     *
     * @param BusinessCustomFieldList $listCollection
     *
     * @return BusinessCustomFieldListCollection
     */
    public function setLists(BusinessCustomFieldList $listCollection)
    {
        $this->lists = $listCollection;

        return $this;
    }

    /**
     * Get listCollection
     *
     * @return BusinessCustomFieldList
     */
    public function getLists()
    {
        return $this->lists;
    }

    /**
     * @param BusinessCustomFieldListItem $value
     *
     * @return BusinessCustomFieldListCollection
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return BusinessCustomFieldListItem
     */
    public function getValue()
    {
        return $this->value;
    }
}
