<?php

namespace Domain\BusinessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="landing_page_short_cut_search")
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\LandingPageShortCutSearchRepository")
 */
class LandingPageShortCutSearch
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\Column(name="title_en", type="string", length=100)
     * @Assert\NotBlank()
     * @Assert\Length(max=100, min=2)
     */
    protected $titleEn;

    /**
     * @ORM\Column(name="title_es", type="string", length=100)
     * @Assert\NotBlank()
     * @Assert\Length(max=100, min=2)
     */
    protected $titleEs;

    /**
     * @ORM\Column(name="search_text_en", type="string", length=100)
     * @Assert\NotBlank()
     * @Assert\Length(max=100, min=2)
     */
    protected $searchTextEn;

    /**
     * @ORM\Column(name="search_text_es", type="string", length=100)
     * @Assert\NotBlank()
     * @Assert\Length(max=100, min=2)
     */
    protected $searchTextEs;

    /**
     * @Gedmo\SortablePosition
     * @ORM\Column(name="position", type="integer", nullable=true)
     */
    protected $position;

    /**
     * @var LandingPageShortCut
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\LandingPageShortCut",
     *     cascade={"persist"},
     *     inversedBy="searchItems",
     * )
     * @ORM\JoinColumn(name="short_cut_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $landingPageShortCut;

    protected $changeState;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getTitleEn() ?: '';
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
    public function getTitleEn()
    {
        return $this->titleEn;
    }

    /**
     * @param string $titleEn
     *
     * @return LandingPageShortCutSearch
     */
    public function setTitleEn($titleEn)
    {
        $this->titleEn = $titleEn;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitleEs()
    {
        return $this->titleEs;
    }

    /**
     * @param string $titleEs
     *
     * @return LandingPageShortCutSearch
     */
    public function setTitleEs($titleEs)
    {
        $this->titleEs = $titleEs;

        return $this;
    }

    /**
     * @return string
     */
    public function getSearchTextEn()
    {
        return $this->searchTextEn;
    }

    /**
     * @param string $searchTextEn
     *
     * @return LandingPageShortCutSearch
     */
    public function setSearchTextEn($searchTextEn)
    {
        $this->searchTextEn = $searchTextEn;

        return $this;
    }

    /**
     * @return string
     */
    public function getSearchTextEs()
    {
        return $this->searchTextEs;
    }

    /**
     * @param mixed $searchTextEs
     *
     * @return LandingPageShortCut
     */
    public function setSearchTextEs($searchTextEs)
    {
        $this->searchTextEs = $searchTextEs;

        return $this;
    }

    /**
     * @return LandingPageShortCut
     */
    public function getLandingPageShortCut()
    {
        return $this->landingPageShortCut;
    }

    /**
     * @param LandingPageShortCut $landingPageShortCut
     *
     * @return LandingPageShortCutSearch
     */
    public function setLandingPageShortCut($landingPageShortCut)
    {
        $this->landingPageShortCut = $landingPageShortCut;

        return $this;
    }

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return LandingPageShortCutSearch
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

    public function getChangeState()
    {
        return $this->changeState;
    }

    public function setChangeState(array $changeState) : self
    {
        $this->changeState = $changeState;

        return $this;
    }
}
