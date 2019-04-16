<?php

namespace Domain\BusinessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Domain\BusinessBundle\Util\ZipFormatterUtil;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Zip
 *
 * @ORM\Table(name="zip")
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\ZipRepository")
 */
class Zip
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
     * @ORM\Column(name="zip_code", type="string", length=10, nullable=true)
     * @Assert\NotBlank()
     * @Assert\Length(max=10, maxMessage="business_profile.max_length")
     */
    protected $zipCode;

    /**
     * @var \Domain\BusinessBundle\Entity\Neighborhood
     *
     * @ORM\ManyToOne(
     *      targetEntity="Domain\BusinessBundle\Entity\Neighborhood",
     *      inversedBy="zips"
     * )
     * @ORM\JoinColumn(name="neighborhood_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $neighborhood;

    protected $changeState;

    /**
     * Constructor
     */
    public function __construct()
    {
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
     * Set zipCode
     *
     * @param string $zipCode
     *
     * @return Zip
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = ZipFormatterUtil::getFormattedZip($zipCode);

        return $this;
    }

    /**
     * Get zipCode
     *
     * @return string
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * Set Neighborhood
     *
     * @param \Domain\BusinessBundle\Entity\Neighborhood $Neighborhood
     *
     * @return Neighborhood
     */
    public function setNeighborhood(\Domain\BusinessBundle\Entity\Neighborhood $neighborhood = null)
    {
        $this->neighborhood = $neighborhood;

        return $this;
    }

    /**
     * Get Neighborhood
     *
     * @return \Domain\BusinessBundle\Entity\Neighborhood
     */
    public function getNeighborhood()
    {
        return $this->neighborhood;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getZipCode() ?: '';
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
