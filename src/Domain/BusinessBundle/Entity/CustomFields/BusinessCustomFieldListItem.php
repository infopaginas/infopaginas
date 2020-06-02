<?php

namespace Domain\BusinessBundle\Entity\CustomFields;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Domain\BusinessBundle\Entity\Translation\CustomFields\BusinessCustomFieldListItemTranslation;
use Oxa\Sonata\AdminBundle\Model\ChangeStateInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\ChangeStateTrait;
use Sonata\TranslationBundle\Model\Gedmo\TranslatableInterface;
use Sonata\TranslationBundle\Traits\Gedmo\PersonalTranslatableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="business_custom_field_list_item")
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\CustomFields\BusinessCustomFieldListItemRepository")
 * @Gedmo\TranslationEntity(
 *     class="Domain\BusinessBundle\Entity\Translation\CustomFields\BusinessCustomFieldListItemTranslation"
 * )
 */
class BusinessCustomFieldListItem implements ChangeStateInterface, TranslatableInterface
{
    use ChangeStateTrait;
    use PersonalTranslatableTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\Column(name="title", type="string", length=100)
     * @Gedmo\Translatable(fallback=true)
     * @Assert\NotBlank()
     * @Assert\Length(max=100, min=1)
     */
    protected $title;

    /**
     * @Gedmo\SortablePosition
     * @ORM\Column(name="position", type="integer", nullable=true)
     */
    protected $position;

    /**
     * @var BusinessCustomFieldList
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\CustomFields\BusinessCustomFieldList",
     *     cascade={"persist"},
     *     inversedBy="listItems",
     * )
     * @ORM\JoinColumn(name="business_custom_field_list_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $businessCustomFieldList;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\Translation\CustomFields\BusinessCustomFieldListItemTranslation",
     *     mappedBy="object",
     *     cascade={"persist", "remove"}
     * )
     */
    protected $translations;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle() ?: '';
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->translations = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return BusinessCustomFieldListItem
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return BusinessCustomFieldList
     */
    public function getBusinessCustomFieldList()
    {
        return $this->businessCustomFieldList;
    }

    /**
     * @param BusinessCustomFieldList $businessCustomFieldList
     *
     * @return BusinessCustomFieldListItem
     */
    public function setBusinessCustomFieldList($businessCustomFieldList)
    {
        $this->businessCustomFieldList = $businessCustomFieldList;

        return $this;
    }

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return BusinessCustomFieldListItem
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Remove translation
     *
     * @param \Domain\BusinessBundle\Entity\Translation\CustomFields\BusinessCustomFieldListItemTranslation $translation
     */
    public function removeTranslation(BusinessCustomFieldListItemTranslation $translation)
    {
        $this->translations->removeElement($translation);
    }
}
