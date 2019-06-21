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
 * @ORM\Table(name="business_custom_field_list")
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\CustomFields\BusinessCustomFieldListRepository")
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\TranslationEntity(
 *     class="Domain\BusinessBundle\Entity\Translation\CustomFields\BusinessCustomFieldListTranslation"
 * )
 * @UniqueEntity("title")
 */
class BusinessCustomFieldList implements DefaultEntityInterface, TranslatableInterface, ChangeStateInterface
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
     * @var BusinessCustomFieldListItem[]
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\CustomFields\BusinessCustomFieldListItem",
     *     mappedBy="businessCustomFieldList",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     *     )
     * @Assert\Valid
     * @Assert\Count(max="10", min="1")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    protected $listItems;

    /**
     * @ORM\OneToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\CustomFields\BusinessCustomFieldListCollection",
     *     mappedBy="lists"
     * )
     */
    private $listCollection;

    /**
     * @var Section
     *
     * @ORM\ManyToOne(
     *      targetEntity="Domain\BusinessBundle\Entity\CustomFields\Section",
     *      inversedBy="businessCustomFieldList"
     * )
     * @ORM\JoinColumn(name="section_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $section;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->listItems = new ArrayCollection();
        $this->listCollection = new ArrayCollection();
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
     * @return BusinessCustomFieldList
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return BusinessCustomFieldListItem[]
     */
    public function getListItems()
    {
        return $this->listItems;
    }

    /**
     * @param BusinessCustomFieldListItem $listItem
     *
     * @return BusinessCustomFieldList
     */
    public function addListItem($listItem)
    {
        $this->listItems[] = $listItem;
        $listItem->setBusinessCustomFieldList($this);

        return $this;
    }

    /**
     * Remove $listItem
     *
     * @param BusinessCustomFieldListItem $listItem
     */
    public function removeListItem($listItem)
    {
        $this->listItems->removeElement($listItem);
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
     * @return BusinessCustomFieldList
     */
    public function setSection($section)
    {
        $this->section = $section;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getListCollection()
    {
        return $this->listCollection;
    }

    /**
     * Add $listCollection
     *
     * @param BusinessCustomFieldListCollection $listCollection
     *
     * @return BusinessProfile
     */
    public function addListCollection(BusinessCustomFieldListCollection $listCollection)
    {
        $this->listCollection[] = $listCollection;

        if ($listCollection) {
            $listCollection->setBusinessProfile($this);
        }

        return $this;
    }

    /**
     * Remove $listCollection
     *
     * @param BusinessCustomFieldListCollection $listCollection
     */
    public function removeListCollection(BusinessCustomFieldListCollection $listCollection)
    {
        $this->listCollection->removeElement($listCollection);
    }
}
