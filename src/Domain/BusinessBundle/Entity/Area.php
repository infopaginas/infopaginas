<?php

namespace Domain\BusinessBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Oxa\Sonata\AdminBundle\Model\CopyableEntityInterface;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Gedmo\Mapping\Annotation as Gedmo;
use Oxa\Sonata\AdminBundle\Util\Traits\OxaPersonalTranslatable as PersonalTranslatable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Sonata\TranslationBundle\Model\Gedmo\TranslatableInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Area
 *
 * @ORM\Table(name="area")
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\AreaRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity("name")
 * @Gedmo\TranslationEntity(class="Domain\BusinessBundle\Entity\Translation\AreaTranslation")
 */
class Area implements DefaultEntityInterface, CopyableEntityInterface, TranslatableInterface
{
    use DefaultEntityTrait;
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
     * @var string - Area name
     *
     * @Gedmo\Translatable
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
     * @var string
     *
     * @ORM\Column(name="search_fts", type="tsvector", options={
     *      "customSchemaOptions": {
     *          "searchFields" : {
     *              "name"
     *          }
     *      }
     *  }, nullable=true)
     *
     */
    protected $searchFts;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function __toString()
    {
        switch (true) {
            case $this->getName():
                $result = $this->getName();
                break;
            case $this->getId():
                $result = sprintf('id(%s): not translated', $this->getId());
                break;
            default:
                $result = 'New area';
        }
        return $result;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->businessProfiles = new ArrayCollection();
        $this->translations     = new ArrayCollection();
        $this->locality         = new ArrayCollection();
    }

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
     * Remove translation
     *
     * @param \Domain\BusinessBundle\Entity\Translation\AreaTranslation $translation
     */
    public function removeTranslation(\Domain\BusinessBundle\Entity\Translation\AreaTranslation $translation)
    {
        $this->translations->removeElement($translation);
    }

    /**
     * Set searchFts
     *
     * @param tsvector $searchFts
     *
     * @return Area
     */
    public function setSearchFts($searchFts)
    {
        $this->searchFts = $searchFts;

        return $this;
    }

    /**
     * Get searchFts
     *
     * @return tsvector
     */
    public function getSearchFts()
    {
        return $this->searchFts;
    }

    /**
     * Add Locality
     *
     * @param  Locality $locality
     * @return this
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
     * @return this
     */
    public function removeLocality(Locality $locality)
    {
        $this->locality->remove($locality);

        return $this;
    }
}
