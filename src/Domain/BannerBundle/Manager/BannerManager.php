<?php

namespace Domain\BannerBundle\Manager;

use Doctrine\ORM\EntityManager;
use Oxa\ManagerArchitectureBundle\Model\Manager\Manager;

/**
 * Class BannerManager
 * Banner management entry point
 *
 * @package Domain\BannerBundle\Manager
 */
class BannerManager extends Manager
{
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
