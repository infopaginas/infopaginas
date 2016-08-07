<?php

namespace Oxa\WistiaBundle\Repository;

use Oxa\WistiaBundle\Entity\WistiaMedia;

/**
 * WistiaMediaEmbedRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class WistiaMediaEmbedRepository extends \Doctrine\ORM\EntityRepository
{
    const SLUG = 'OxaWistiaBundle:WistiaMediaEmbed';

    /**
     * @param WistiaMedia $wistiaMedia
     * @return null|object
     */
    public function findByMediaObjectWithoutDimensions(WistiaMedia $wistiaMedia)
    {
        return $this->findOneBy([
            'media'          => $wistiaMedia,
            'autoDimensions' => true,
        ]);
    }

    /**
     * @param WistiaMedia $wistiaMedia
     * @param int $height
     * @return null|object
     */
    public function findByMediaObjectAndHeight(WistiaMedia $wistiaMedia, int $height)
    {
        return $this->findOneBy([
            'media'          => $wistiaMedia,
            'height'         => $height,
            'autoDimensions' => true,
        ]);
    }

    /**
     * @param WistiaMedia $wistiaMedia
     * @param int $width
     * @return null|object
     */
    public function findByMediaObjectAndWidth(WistiaMedia $wistiaMedia, int $width)
    {
        return $this->findOneBy([
            'media'          => $wistiaMedia,
            'width'          => $width,
            'autoDimensions' => true,
        ]);
    }

    /**
     * @param WistiaMedia $wistiaMedia
     * @param int $width
     * @param int $height
     * @return null|object
     */
    public function findByMediaObjectWithDimensions(WistiaMedia $wistiaMedia, int $width, int $height)
    {
        return $this->findOneBy([
            'media'  => $wistiaMedia,
            'width'  => $width,
            'height' => $height,
        ]);
    }
}