<?php

namespace Domain\BusinessBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="landing_page_short_cut")
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\LandingPageShortCutRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity("locality")
 */
class LandingPageShortCut implements DefaultEntityInterface
{
    use DefaultEntityTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var $locality
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\Locality")
     * @ORM\JoinColumn(name="locality_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    protected $locality;

    /**
     * @var LandingPageShortCutSearch[]
     *
     * @ORM\OneToMany(
     *     targetEntity="Domain\BusinessBundle\Entity\LandingPageShortCutSearch",
     *     mappedBy="landingPageShortCut",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     *     )
     * @Assert\Valid
     * @Assert\Count(max="10", min="1")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    protected $searchItems;

    /**
     * @var bool
     *
     * @ORM\Column(name="use_all_location", type="boolean", options={"default" : 0})
     */
    protected $useAllLocation;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->searchItems = new ArrayCollection();
        $this->isActive = false;
        $this->useAllLocation = false;
    }

    public function __toString()
    {
        return $this->getLocality() ? $this->getLocality()->getName() : (string)$this->getId();
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
     * @return Locality|null
     */
    public function getLocality()
    {
        return $this->locality;
    }

    /**
     * @param Locality|null $locality
     *
     * @return LandingPageShortCut
     */
    public function setLocality($locality)
    {
        $this->locality = $locality;

        return $this;
    }

    /**
     * @return LandingPageShortCutSearch[]
     */
    public function getSearchItems()
    {
        return $this->searchItems;
    }

    /**
     * @param LandingPageShortCutSearch $searchItem
     *
     * @return LandingPageShortCut
     */
    public function addSearchItem($searchItem)
    {
        $this->searchItems[] = $searchItem;
        $searchItem->setLandingPageShortCut($this);

        return $this;
    }

    /**
     * Remove $searchItem
     *
     * @param LandingPageShortCutSearch $searchItem
     */
    public function removeSearchItem($searchItem)
    {
        $this->searchItems->removeElement($searchItem);
    }

    /**
     * @return boolean
     */
    public function getUseAllLocation()
    {
        return $this->useAllLocation;
    }

    /**
     * @param boolean $useAllLocation
     *
     * @return LandingPageShortCut
     */
    public function setUseAllLocation($useAllLocation)
    {
        $this->useAllLocation = $useAllLocation;

        return $this;
    }
}
