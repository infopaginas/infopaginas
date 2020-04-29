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
 * @ORM\Table(name="business_custom_field_text_area")
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\CustomFields\BusinessCustomFieldTextAreaRepository")
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\TranslationEntity(
 *     class="Domain\BusinessBundle\Entity\Translation\CustomFields\BusinessCustomFieldTextAreaTranslation"
 * )
 * @UniqueEntity("title")
 */
class BusinessCustomFieldTextArea implements DefaultEntityInterface, TranslatableInterface, ChangeStateInterface
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
     *     targetEntity="Domain\BusinessBundle\Entity\CustomFields\BusinessCustomFieldTextAreaCollection",
     *     mappedBy="textAreas"
     * )
     */
    private $textAreaCollection;

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
        $this->textAreaCollection = new ArrayCollection();
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
     * @return BusinessCustomFieldTextArea
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return BusinessCustomFieldTextAreaCollection|null
     */
    public function getTextAreaCollection()
    {
        return $this->textAreaCollection;
    }

    /**
     * @param BusinessCustomFieldTextAreaCollection|null $textAreaCollection
     *
     * @return BusinessCustomFieldTextArea
     */
    public function setTextAreaCollection($textAreaCollection)
    {
        $this->textAreaCollection = $textAreaCollection;

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
     * @return BusinessCustomFieldTextArea
     */
    public function setSection($section)
    {
        $this->section = $section;

        return $this;
    }

    /**
     * @param boolean $hideTitle
     *
     * @return BusinessCustomFieldTextArea
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
