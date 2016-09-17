<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 30.08.16
 * Time: 14:08
 */

namespace Domain\BusinessBundle\Model\DataType;

use Doctrine\Common\Collections\ArrayCollection;
use Oxa\ManagerArchitectureBundle\Model\Interfaces\DataTransferObjectInterface;
use Oxa\Sonata\MediaBundle\Entity\Media;

/**
 * Class MediaDTO
 * @package Domain\BusinessBundle\Model\DataType
 */
class MediaDTO implements DataTransferObjectInterface
{
    /**
     * @var Media $medias
     */
    protected $media;

    /**
     * MediaDTO constructor.
     * @param $media
     */
    public function __construct($media)
    {
        $this->media = $media;
    }

    /**
     * @return string
     */
    public function serialize() : string
    {
        return json_encode($this->media);
    }

    /**
     * @param string $serialized
     * @return mixed
     */
    public static function deserialize(string $serialized)
    {
        return json_decode($serialized);
    }
}
