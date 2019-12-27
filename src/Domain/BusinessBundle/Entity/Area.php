<?php

namespace Domain\BusinessBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Domain\BusinessBundle\Entity\Translation\AreaTranslation;
use Oxa\Sonata\AdminBundle\Model\ChangeStateInterface;
use Oxa\Sonata\AdminBundle\Model\CopyableEntityInterface;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Model\OxaPersonalTranslatableInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\ChangeStateTrait;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Gedmo\Mapping\Annotation as Gedmo;
use Oxa\Sonata\AdminBundle\Util\Traits\OxaPersonalTranslatable as PersonalTranslatable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Area
 *
 * @ORM\Table(name="area")
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\AreaRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity("name")
 * @Gedmo\TranslationEntity(class="Domain\BusinessBundle\Entity\Translation\AreaTranslation")
 */
class Area implements
    DefaultEntityInterface,
    CopyableEntityInterface,
    OxaPersonalTranslatableInterface,
    ChangeStateInterface
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
     * @var string - Area name
     *
     * @Gedmo\Translatable(fallback=true)
     * @ORM\Column(name="name", type="string", length=100)
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @ORM\ManyToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\BusinessProfile",
     *     mappedBy="areas",
     *     cascade={"persist"}
     *     )
     */
    protected $businessProfiles;

    /**
     * @ORM\ManyToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\BusinessProfileExtraSearch",
     *     mappedBy="areas"
     * )
     */
    private $extraSearches;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\Translation\AreaTranslation",
     *     mappedBy="object",
     *     cascade={"persist", "remove"}
     * )
     */
    protected $translations;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="Domain\BusinessBundle\Entity\Locality",
     *      mappedBy="area",
     *      cascade={"persist"}
     * )
     */
    protected $locality;

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
    public function __toString()
    {
        return $this->getName() ?: '';
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->businessProfiles = new ArrayCollection();
        $this->translations     = new ArrayCollection();
        $this->locality         = new ArrayCollection();
        $this->extraSearches    = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getMarkCopyPropertyName()
    {
        return 'name';
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Area
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add businessProfile
     *
     * @param \Domain\BusinessBundle\Entity\BusinessProfile $businessProfile
     *
     * @return Area
     */
    public function addBusinessProfile(\Domain\BusinessBundle\Entity\BusinessProfile $businessProfile)
    {
        $this->businessProfiles[] = $businessProfile;
        $businessProfile->addArea($this);

        return $this;
    }

    /**
     * Remove businessProfile
     *
     * @param \Domain\BusinessBundle\Entity\BusinessProfile $businessProfile
     */
    public function removeBusinessProfile(\Domain\BusinessBundle\Entity\BusinessProfile $businessProfile)
    {
        $this->businessProfiles->removeElement($businessProfile);
        $businessProfile->removeArea($this);
    }

    /**
     * Get businessProfiles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBusinessProfiles()
    {
        return $this->businessProfiles;
    }

    /**
     * Add Locality
     *
     * @param  Locality $locality
     * @return Area
     */
    public function addLocality(Locality $locality)
    {
        $this->locality->add($locality);

        return $this;
    }

    /**
     * Get Locality
     *
     * @return Locality[]
     */
    public function getLocalities()
    {
        return $this->locality;
    }

    /**
     * Add Locality
     *
     * @param  Locality $locality
     * @return Area
     */
    public function removeLocality(Locality $locality)
    {
        $this->locality->remove($locality);

        return $this;
    }

    /**
     * Get locality
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLocality()
    {
        return $this->locality;
    }

    /**
     * Add $extraSearch
     *
     * @param BusinessProfileExtraSearch $extraSearch
     *
     * @return Area
     */
    public function addExtraSearch($extraSearch)
    {
        $this->extraSearches[] = $extraSearch;
        $extraSearch->addArea($this);
    }

    /**
     * @param BusinessProfileExtraSearch $extraSearch
     *
     * @return Area
     */
    public function removeExtraSearch(BusinessProfileExtraSearch $extraSearch)
    {
        $this->extraSearches->removeElement($extraSearch);
        $extraSearch->removeArea($this);

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getExtraSearches()
    {
        return $this->extraSearches;
    }

    public function getTranslationClass(): string
    {
        return AreaTranslation::class;
    }
}
