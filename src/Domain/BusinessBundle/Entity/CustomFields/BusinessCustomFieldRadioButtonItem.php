<?php

namespace Domain\BusinessBundle\Entity\CustomFields;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Domain\BusinessBundle\Entity\Translation\CustomFields\BusinessCustomFieldRadioButtonItemTranslation;
use Oxa\Sonata\AdminBundle\Model\ChangeStateInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\ChangeStateTrait;
use Sonata\TranslationBundle\Model\Gedmo\TranslatableInterface;
use Sonata\TranslationBundle\Traits\Gedmo\PersonalTranslatable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="business_custom_field_radio_button_item")
 * @ORM\Entity(
 *     repositoryClass="Domain\BusinessBundle\Repository\CustomFields\BusinessCustomFieldRadioButtonItemRepository"
 * )
 * @Gedmo\TranslationEntity(
 *     class="Domain\BusinessBundle\Entity\Translation\CustomFields\BusinessCustomFieldRadioButtonItemTranslation"
 * )
 * @UniqueEntity("title")
 */
class BusinessCustomFieldRadioButtonItem implements ChangeStateInterface, TranslatableInterface
{
    use ChangeStateTrait;
    use PersonalTranslatable;

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
     * @var BusinessCustomFieldRadioButton
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\CustomFields\BusinessCustomFieldRadioButton",
     *     cascade={"persist"},
     *     inversedBy="radioButtonItems",
     * )
     * @ORM\JoinColumn(name="business_custom_field_radio_button_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $businessCustomFieldRadioButton;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\Translation\CustomFields\BusinessCustomFieldRadioButtonItemTranslation",
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
     * @return BusinessCustomFieldRadioButtonItem
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return BusinessCustomFieldRadioButton
     */
    public function getBusinessCustomFieldRadioButton()
    {
        return $this->businessCustomFieldRadioButton;
    }

    /**
     * @param BusinessCustomFieldRadioButton $businessCustomFieldRadioButton
     *
     * @return BusinessCustomFieldRadioButtonItem
     */
    public function setBusinessCustomFieldRadioButton($businessCustomFieldRadioButton)
    {
        $this->businessCustomFieldRadioButton = $businessCustomFieldRadioButton;

        return $this;
    }

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return BusinessCustomFieldRadioButtonItem
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
     * @param BusinessCustomFieldRadioButtonItemTranslation $translation
     */
    public function removeTranslation(BusinessCustomFieldRadioButtonItemTranslation $translation)
    {
        $this->translations->removeElement($translation);
    }
}
