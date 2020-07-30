<?php

namespace Domain\BusinessBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Domain\BusinessBundle\Util\ZipFormatterUtil;
use Oxa\Sonata\AdminBundle\Model\ChangeStateInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\ChangeStateTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Zip
 *
 * @ORM\Table(name="zip")
 * @ORM\Entity(repositoryClass="Domain\BusinessBundle\Repository\ZipRepository")
 */
class Zip implements ChangeStateInterface
{
    use ChangeStateTrait;

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
     * @var Neighborhood
     *
     * @ORM\ManyToOne(
     *      targetEntity="Domain\BusinessBundle\Entity\Neighborhood",
     *      inversedBy="zips"
     * )
     * @ORM\JoinColumn(name="neighborhood_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $neighborhood;

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    public function __toString()
    {
        return $this->getZipCode() ?: '';
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

    public function setNeighborhood(Neighborhood $neighborhood = null)
    {
        $this->neighborhood = $neighborhood;

        return $this;
    }

    public function getNeighborhood()
    {
        return $this->neighborhood;
    }
}
