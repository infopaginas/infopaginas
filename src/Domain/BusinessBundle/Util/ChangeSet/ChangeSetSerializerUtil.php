<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 31.08.16
 * Time: 22:45
 */

namespace Domain\BusinessBundle\Util\ChangeSet;

use Doctrine\ORM\EntityManagerInterface;
use Domain\BusinessBundle\Entity\Media\BusinessGallery;
use Domain\BusinessBundle\Util\DoctrineUtil;
use Oxa\WistiaBundle\Entity\WistiaMedia;

/**
 * Class ChangeSetSerializerUtil
 * @package Domain\BusinessBundle\Util\ChangeSet
 */
class ChangeSetSerializerUtil
{
    /**
     * @param BusinessGallery $businessGallery
     * @return string
     */
    public static function serializeBusinessGalleryObject(BusinessGallery $businessGallery) : string
    {
        $businessGalleryRequiredData = [
            'id'          => $businessGallery->getId(),
            'media'       => $businessGallery->getMedia()->getId(),
            'name'        => $businessGallery->getMedia()->getName(),
            'description' => $businessGallery->getDescription(),
            'isPrimary'   => $businessGallery->getIsPrimary(),
            'type'        => $businessGallery->getType(),
        ];

        return self::serializeObject($businessGalleryRequiredData);
    }

    /**
     * @param EntityManagerInterface $em
     * @param BusinessGallery $businessGallery
     * @return string
     */
    public static function serializeBusinessGalleryDiff(
        EntityManagerInterface $em,
        BusinessGallery $businessGallery
    ) : string {
        $diff = DoctrineUtil::diffDoctrineObject($em, $businessGallery);
        $diff['id']    = $businessGallery->getId();
        $diff['image'] = $businessGallery->getMedia()->getName();

        return self::serializeObject($diff);
    }

    /**
     * @param WistiaMedia $video
     * @return string
     */
    public static function serializeBusinessProfileVideo(WistiaMedia $video) : string
    {
        $businessVideoRequiredData = [
            'id'   => $video->getId(),
            'name' => $video->getName(),
        ];

        return self::serializeObject($businessVideoRequiredData);
    }

    /**
     * @param array $data
     * @return string
     */
    public static function serializeObject(array $data) : string
    {
        return json_encode($data);
    }
}