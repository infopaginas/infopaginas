<?php

namespace Domain\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Oxa\Sonata\AdminBundle\Model\ChangeStateInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\ChangeStateTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Banner
 *
 * @ORM\Table(name="release_notes")
 * @ORM\Entity()
 */
class ReleaseNote implements ChangeStateInterface
{
    use TimestampableEntity;
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
     * @var string - Release version
     *
     * @ORM\Column(name="version", type="string", length=20)
     * @Assert\NotBlank()
     */
    protected $version;

    /**
     * @var string - Change notes
     *
     * @ORM\Column(name="description", type="text")
     * @Assert\NotBlank()
     */
    protected $description;

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
        return $this->getVersion() ?: '';
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param string $version
     *
     * @return ReleaseNote
     */
    public function setVersion(string $version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return ReleaseNote
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
