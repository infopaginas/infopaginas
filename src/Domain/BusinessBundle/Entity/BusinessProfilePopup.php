<?php

namespace Domain\BusinessBundle\Entity;

use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Model\FileUploadEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Oxa\Sonata\AdminBundle\Util\Traits\FileUploadEntityTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * BusinessProfile
 *
 * @ORM\Table(name="business_profile_popup")
 * @ORM\Entity()
 */
class BusinessProfilePopup implements DefaultEntityInterface, FileUploadEntityInterface
{
    use DefaultEntityTrait;
    use FileUploadEntityTrait;

    public const FILE_MIME_TYPE = 'application/pdf';
    public const FILE_EXTENSION = 'pdf';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string - Popup message
     *
     * @ORM\Column(type="string", nullable=true, length=1000)
     * @Assert\NotBlank()
     */
    protected $message;

    public function __toString()
    {
        return 'id: ' . $this->getId();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return BusinessProfilePopup
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    public function getFileExtension(): string
    {
        return self::FILE_EXTENSION;
    }

    public function getFileMimeType(): string
    {
        return self::FILE_MIME_TYPE;
    }
}
