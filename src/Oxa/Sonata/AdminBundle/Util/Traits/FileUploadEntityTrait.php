<?php

namespace Oxa\Sonata\AdminBundle\Util\Traits;

/**
 * Should be added to all entities for extended CRUD functional
 *
 * Class DefaultEntityTrait
 * @package Oxa\Sonata\AdminBundle\Util\Traits
 */
trait FileUploadEntityTrait
{
    /**
     * @var string - File path
     *
     * @ORM\Column(name="file", type="string", length=1000)
     * @Assert\Length(max=1000)
     * @Assert\NotBlank()
     */
    protected $file;

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param string $file
     *
     * @return FileUploadEntityTrait
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }
}
