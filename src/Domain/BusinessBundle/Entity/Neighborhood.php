<?php

namespace Domain\BusinessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Domain\BusinessBundle\Entity\Area;
use Oxa\GeolocationBundle\Model\Geolocation\GeolocationInterface;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Gedmo\Mapping\Annotation as Gedmo;
use Oxa\Sonata\AdminBundle\Util\Traits\OxaPersonalTranslatable as PersonalTranslatable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Sonata\TranslationBundle\Model\Gedmo\TranslatableInterface;

/**
 * Neighborhood
 *
 * @ORM\Table(name="neighborhood")
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\NeighborhoodRepository")
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\TranslationEntity(class="Domain\BusinessBundle\Entity\Translation\NeighborhoodTranslation")
 */
class Neighborhood implements DefaultEntityInterface, TranslatableInterface
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
    private $id;

    /**
     * @var string - Neighborhood name
     *
     * @Gedmo\Translatable(fallback=true)
     * @ORM\Column(name="name", type="string", length=100)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\ManyToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\BusinessProfile",
     *     mappedBy="neighborhoods",
     *     cascade={"persist"}
     * )
     */
    private $businessProfile;

    /**
     * @var Locality
     *
     * @ORM\ManyToOne(
     *      targetEntity="Domain\BusinessBundle\Entity\Locality",
     *      inversedBy="neighborhoods"
     * )
     * @ORM\JoinColumn(name="locality_id", referencedColumnName="id")
     */
    protected $locality;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *      targetEntity="Domain\BusinessBundle\Entity\Zip",
     *      mappedBy="neighborhood",
     *      cascade={"persist", "remove"},
     *      orphanRemoval=true
     * )
     */
    protected $zips;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\Translation\NeighborhoodTranslation",
     *     mappedBy="object",
     *     cascade={"persist", "remove"}
     * )
     */
    protected $translations;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->businessProfile  = new ArrayCollection();
        $this->translations     = new ArrayCollection();
        $this->zips             = new ArrayCollection();
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
     * @return Neighborhood
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
     * @return Neighborhood
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
     * @return Neighborhood
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
     *  Get owning Locality for this Neighborhood
     *
     * @return Locality
     */
    public function getLocality()
    {
        return $this->locality;
    }

    /**
     *  Set owning Locality for this Neighborhood
     *
     * @param Locality|null $locality
     * @return $this
     */
    public function setLocality($locality)
    {
        $this->locality = $locality;

        return $this;
    }

    /**
     * Remove translation
     *
     * @param \Domain\BusinessBundle\Entity\Translation\NeighborhoodTranslation $translation
     */
    public function removeTranslation(\Domain\BusinessBundle\Entity\Translation\NeighborhoodTranslation $translation)
    {
        $this->translations->removeElement($translation);
    }

    /**
     * Get Locality
     *
     * @return Zip[]
     */
    public function getZips()
    {
        return $this->zips;
    }

    /**
     * Add zip
     *
     * @param \Domain\BusinessBundle\Entity\Zip $zip
     *
     * @return BusinessProfile
     */
    public function addZip(\Domain\BusinessBundle\Entity\Zip $zip)
    {
        $this->zips[] = $zip;

        $zip->setNeighborhood($this);

        return $this;
    }

    /**
     * Remove zip
     *
     * @param \Domain\BusinessBundle\Entity\Zip $zip
     */
    public function removeZip(\Domain\BusinessBundle\Entity\Zip $zip)
    {
        $this->zips->removeElement($zip);
    }


    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName() ?: '';
    }
}
