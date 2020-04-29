<?php

namespace Domain\BusinessBundle\Entity\CustomFields;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Oxa\Sonata\AdminBundle\Model\ChangeStateInterface;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\ChangeStateTrait;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Gedmo\Mapping\Annotation as Gedmo;
use Sonata\TranslationBundle\Model\Gedmo\TranslatableInterface;
use Sonata\TranslationBundle\Traits\Gedmo\PersonalTranslatable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="business_custom_field_radio_button")
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\CustomFields\BusinessCustomFieldRadioButtonRepository")
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\TranslationEntity(
 *     class="Domain\BusinessBundle\Entity\Translation\CustomFields\BusinessCustomFieldRadioButtonTranslation"
 * )
 * @UniqueEntity("title")
 */
class BusinessCustomFieldRadioButton implements DefaultEntityInterface, TranslatableInterface, ChangeStateInterface
{
    use DefaultEntityTrait;
    use PersonalTranslatable;
    use ChangeStateTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @Gedmo\Translatable(fallback=true)
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     * @Assert\Length(max=100, min=1)
     */
    protected $title;

    /**
     * @var BusinessCustomFieldRadioButtonItem[]
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\CustomFields\BusinessCustomFieldRadioButtonItem",
     *     mappedBy="businessCustomFieldRadioButton",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     *     )
     * @Assert\Valid
     * @Assert\Count(min="1")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    protected $radioButtonItems;

    /**
     * @ORM\OneToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\CustomFields\BusinessCustomFieldRadioButtonCollection",
     *     mappedBy="radioButtons"
     * )
     */
    private $radioButtonCollection;

    /**
     * @var Section
     *
     * @ORM\ManyToOne(
     *      targetEntity="Domain\BusinessBundle\Entity\CustomFields\Section"
     * )
     * @ORM\JoinColumn(name="section_id", referencedColumnName="id", onDelete="SET NULL")
     * @Assert\NotBlank()
     */
    protected $section;

    /**
     * @var bool
     *
     * @ORM\Column(name="hide_title", type="boolean", nullable=true)
     */
    private $hideTitle;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->radioButtonItems = new ArrayCollection();
        $this->radioButtonCollection = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle() ?: '';
    }

    /**
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
     * @return BusinessCustomFieldRadioButton
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return BusinessCustomFieldRadioButtonItem[]
     */
    public function getRadioButtonItems()
    {
        return $this->radioButtonItems;
    }

    /**
     * @param BusinessCustomFieldRadioButtonItem $radioButtonItem
     *
     * @return BusinessCustomFieldRadioButton
     */
    public function addRadioButtonItem($radioButtonItem)
    {
        $this->radioButtonItems[] = $radioButtonItem;
        $radioButtonItem->setBusinessCustomFieldRadioButton($this);

        return $this;
    }

    /**
     * Remove $radioButtonItem
     *
     * @param BusinessCustomFieldRadioButtonItem $radioButtonItem
     */
    public function removeRadioButtonItem($radioButtonItem)
    {
        $this->radioButtonItems->removeElement($radioButtonItem);
    }

    /**
     * @return Section|null
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * @param Section|null $section
     *
     * @return BusinessCustomFieldRadioButton
     */
    public function setSection($section)
    {
        $this->section = $section;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getRadioButtonCollection()
    {
        return $this->radioButtonCollection;
    }

    /**
     * Add $radioButtonCollection
     *
     * @param BusinessCustomFieldRadioButtonCollection $radioButtonCollection
     *
     * @return BusinessProfile
     */
    public function addRadioButtonCollection(BusinessCustomFieldRadioButtonCollection $radioButtonCollection)
    {
        $this->radioButtonCollection[] = $radioButtonCollection;

        if ($radioButtonCollection) {
            $radioButtonCollection->setBusinessProfile($this);
        }

        return $this;
    }

    /**
     * Remove $radioButtonCollection
     *
     * @param BusinessCustomFieldRadioButtonCollection $radioButtonCollection
     */
    public function removeRadioButtonCollection(BusinessCustomFieldRadioButtonCollection $radioButtonCollection)
    {
        $this->radioButtonCollection->removeElement($radioButtonCollection);
    }

    /**
     * @param boolean $hideTitle
     *
     * @return BusinessCustomFieldRadioButton
     */
    public function setHideTitle($hideTitle)
    {
        $this->hideTitle = $hideTitle;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getHideTitle()
    {
        return $this->hideTitle;
    }
}
