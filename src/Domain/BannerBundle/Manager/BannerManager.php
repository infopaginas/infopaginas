<?php

namespace Domain\BannerBundle\Manager;

use Doctrine\ORM\EntityManager;

class BannerManager
{
    protected $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function getBanner($bannerType)
    {
        return '<img src="/bundles/domainsite/images/banner.png">';
    }
}
