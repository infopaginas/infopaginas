<?php
/**
 * Created by PhpStorm.
 * User: Xedin
 * Date: 12.06.16
 * Time: 15:34
 */

namespace Oxa\WistiaBundle\Manager;


use Doctrine\ORM\EntityManager;
use Oxa\WistiaBundle\Entity\WistiaMedia;

class WistiaMediaManager
{
    private $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function save(array $wistiaMediaData) : WistiaMedia
    {
        $wistiaMedia = new WistiaMedia($wistiaMediaData);

        $this->em->persist($wistiaMedia);
        $this->em->flush();

        return $wistiaMedia;
    }
}