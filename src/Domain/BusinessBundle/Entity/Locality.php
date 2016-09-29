<?php

namespace Domain\BusinessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Domain\BusinessBundle\Entity\Area;
use Oxa\GeolocationBundle\Utils\Traits\LocationTrait;
use Oxa\GeolocationBundle\Model\Geolocation\GeolocationInterface;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Gedmo\Mapping\Annotation as Gedmo;
use Oxa\Sonata\AdminBundle\Util\Traits\OxaPersonalTranslatable as PersonalTranslatable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Sonata\TranslationBundle\Model\Gedmo\TranslatableInterface;

/**
 * Locality
 *
 * @ORM\Table(name="locality")
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\LocalityRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\TranslationEntity(class="Domain\BusinessBundle\Entity\Translation\LocalityTranslation")
 */
class Locality implements GeolocationInterface, DefaultEntityInterface, TranslatableInterface
{
    use DefaultEntityTrait;
    use LocationTrait;
    use PersonalTranslatable;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string - Locality name
     *
     * @Gedmo\Translatable(fallback=true)
     * @ORM\Column(name="name", type="string", length=100)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\ManyToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\BusinessProfile",
     *     mappedBy="localities",
     *     cascade={"persist"}
     * )
     */
    private $businessProfile;

    /**
     * @var Domain\BusinessBundle\Entity\Area
     *
     * @ORM\ManyToOne(
     *      targetEntity="Domain\BusinessBundle\Entity\Area",
     *      inversedBy="locality"
     * )
     * @ORM\JoinColumn(name="area_id", referencedColumnName="id")
     */
    protected $area;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="Domain\BusinessBundle\Entity\Neighborhood",
     *      mappedBy="locality",
     *      cascade={"persist"}
     * )
     */
    protected $neighborhoods;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\Translation\LocalityTranslation",
     *     mappedBy="object",
     *     cascade={"persist", "remove"}
     * )
     */
    protected $translations;

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
     * Constructor
     */
    public function __construct()
    {
        $this->businessProfile  = new ArrayCollection();
        $this->translations     = new ArrayCollection();
        $this->neighborhoods    = new ArrayCollection();
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Locality
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBusinessProfile()
    {
        return $this->businessProfile;
    }

    /**
     * @param mixed $businessProfile
     * @return Locality
     */
    public function setBusinessProfile($businessProfile)
    {
        $this->businessProfile = $businessProfile;
        return $this;
    }

    /**
     * Add businessProfile
     *
     * @param \Domain\BusinessBundle\Entity\BusinessProfile $businessProfile
     *
     * @return Locality
     */
    public function addBusinessProfile(\Domain\BusinessBundle\Entity\BusinessProfile $businessProfile)
    {
        $this->businessProfile[] = $businessProfile;
    }

    /**
     * @param BusinessProfile $businessProfile
     * @return $this
     */
    public function removeBusinessProfile(\Domain\BusinessBundle\Entity\BusinessProfile $businessProfile)
    {
        $this->businessProfile->removeElement($businessProfile);

        return $this;
    }

    /**
     *  Get owning area for this locality
     *
     * @return Area
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     *  Set owning area for this locality
     *
     * @param Area $area
     * @return $this
     */
    public function setArea(Area $area)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * Get Neighborhoods
     *
     * @return Neighborhood[]
     */
    public function getNeighborhoods()
    {
        return $this->neighborhoods;
    }

    /**
     * Remove translation
     *
     * @param \Domain\BusinessBundle\Entity\Translation\LocalityTranslation $translation
     */
    public function removeTranslation(\Domain\BusinessBundle\Entity\Translation\LocalityTranslation $translation)
    {
        $this->translations->removeElement($translation);
    }

    /**
     * Set searchFts
     *
     * @param tsvector $searchFts
     *
     * @return Locality
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
     * @return string
     */
    public function __toString()
    {
        return $this->getName() ?: '';
    }
}
