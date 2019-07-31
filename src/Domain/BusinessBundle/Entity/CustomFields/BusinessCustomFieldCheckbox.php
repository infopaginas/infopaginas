<?php

namespace Domain\BusinessBundle\Entity\CustomFields;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Domain\BusinessBundle\Entity\BusinessCustomFieldCheckboxCollection;
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
 * @ORM\Table(name="business_custom_field_checkbox")
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\CustomFields\BusinessCustomFieldCheckboxRepository")
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\TranslationEntity(
 *     class="Domain\BusinessBundle\Entity\Translation\CustomFields\BusinessCustomFieldCheckboxTranslation"
 * )
 * @UniqueEntity("title")
 */
class BusinessCustomFieldCheckbox implements DefaultEntityInterface, TranslatableInterface, ChangeStateInterface
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
     * @ORM\OneToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\CustomFields\BusinessCustomFieldCheckboxCollection",
     *     mappedBy="checkboxes"
     * )
     */
    private $checkboxCollection;

    /**
     * @var Section
     *
     * @ORM\ManyToOne(
     *      targetEntity="Domain\BusinessBundle\Entity\CustomFields\Section",
     *      inversedBy="businessCustomFieldCheckbox"
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
        $this->checkboxCollection = new ArrayCollection();
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
     * @return BusinessCustomFieldCheckbox
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
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
     * @return BusinessCustomFieldCheckbox
     */
    public function setSection($section)
    {
        $this->section = $section;

        return $this;
    }

    /**
     * @return \Domain\BusinessBundle\Entity\CustomFields\BusinessCustomFieldCheckboxCollection|null
     */
    public function getCheckboxCollection()
    {
        return $this->checkboxCollection;
    }

    /**
     * @param \Domain\BusinessBundle\Entity\CustomFields\BusinessCustomFieldCheckboxCollection|null $checkboxCollection
     *
     * @return BusinessCustomFieldCheckbox
     */
    public function setCheckboxCollection($checkboxCollection)
    {
        $this->checkboxCollection = $checkboxCollection;

        return $this;
    }

    /**
     * @param boolean $hideTitle
     *
     * @return BusinessCustomFieldCheckbox
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
