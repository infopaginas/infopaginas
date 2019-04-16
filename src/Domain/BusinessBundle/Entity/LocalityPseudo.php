<?php

namespace Domain\BusinessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * LocalityPseudo
 *
 * @ORM\Table(name="locality_pseudo")
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\LocalityPseudoRepository")
 */
class LocalityPseudo
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100)
     */
    private $name;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"name"}, updatable=false)
     * @ORM\Column(name="slug", type="string", length=100)
     */
    protected $slug;

    /**
     * @var Locality
     * @ORM\ManyToOne(targetEntity="Domain\BusinessBundle\Entity\Locality",
     *     cascade={"persist"},
     *     inversedBy="pseudos",
     * )
     * @ORM\JoinColumn(name="locality_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $locality;

    protected $changeState;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName() ?: '';
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
     *
     * @return LocalityPseudo
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     *
     * @return LocalityPseudo
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Set Locality
     *
     * @param \Domain\BusinessBundle\Entity\Locality $locality
     *
     * @return LocalityPseudo
     */
    public function setLocality($locality = null)
    {
        $this->locality = $locality;

        return $this;
    }

    /**
     * Get locality
     *
     * @return Locality
     */
    public function getLocality()
    {
        return $this->locality;
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
