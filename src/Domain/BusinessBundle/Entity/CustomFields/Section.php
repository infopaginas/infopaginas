<?php

namespace Domain\BusinessBundle\Entity\CustomFields;

use Doctrine\ORM\Mapping as ORM;
use Oxa\Sonata\AdminBundle\Model\ChangeStateInterface;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\ChangeStateTrait;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Gedmo\Mapping\Annotation as Gedmo;
use Sonata\TranslationBundle\Model\Gedmo\TranslatableInterface;
use Sonata\TranslationBundle\Traits\Gedmo\PersonalTranslatable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="section")
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\CustomFields\SectionRepository")
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\TranslationEntity(class="Domain\BusinessBundle\Entity\Translation\CustomFields\SectionTranslation")
 * @UniqueEntity("title")
 */
class Section implements DefaultEntityInterface, TranslatableInterface, ChangeStateInterface
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
     */
    protected $title;

    /**
     * @Gedmo\SortablePosition
     * @ORM\Column(name="position", type="integer", nullable=true)
     */
    protected $position;

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
     * Set position
     *
     * @param integer $position
     *
     * @return Section
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
}
