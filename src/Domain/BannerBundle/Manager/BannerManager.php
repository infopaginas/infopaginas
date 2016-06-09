<?php

namespace Domain\BannerBundle\Manager;

use Doctrine\ORM\EntityManager;

/**
 * Class BannerManager
 * Banner management entry point
 *
 * @package Domain\BannerBundle\Manager
 */
class BannerManager
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * BannerManager constructor.
     *
     * @access public
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * factory-like methid for retrieving banners for different places
     *
     * @param int $bannerType
     * @return string|object
     */
    public function getBanner(int $bannerType)
    {
        return '<img src="/bundles/domainsite/images/banner.png">';
    }
}
